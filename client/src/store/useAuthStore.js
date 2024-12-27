import { create } from "zustand";
import { axiosInstance } from "../lib/axios";
import toast from "react-hot-toast";
import Pusher from "pusher-js";

export const useAuthStore = create((set, get) => ({
    authUser: null,
    isSignUp: false,
    isLogin: false,
    isUpdatingProfile: false,

    isCheckingAuth: false,
    onlineUsers: [],
    pusher: null,
    messages: [],
    users: [],
    selectedUser: null,
    isUsersLoading: false,
    isMessagesLoading: false,

    setMessages: (newMessage) => {
        set((state) => ({
            messages: [...state.messages, newMessage],
        }));
    },

    initializePusher: () => {
        const { authUser } = get();
        if (!authUser) return;

        const pusher = new Pusher("9a5027a440d42c7d94b5", {
            cluster: "ap3",
            forceTLS: true,
        });

        const channel = pusher.subscribe(`user${authUser?.id}`);
        const channel2 = pusher.subscribe(`online-users`);

        channel.bind("pusher:subscription_succeeded", () => {
            console.log("Successfully subscribed to channel");
        });

        channel2.bind("pusher:subscription_succeeded", () => {
            console.log("channel2 Successfully subscribed to channel");
        });

        channel.bind("message", (data) => {
            const { selectedUser } = get();
            if (!selectedUser) return;
            const isMessageSentFromSelectedUser =
                data.message.sender_id === selectedUser.id;
            if (!isMessageSentFromSelectedUser) return;
            get().setMessages(data.message);
        });

        channel2.bind("getOnlineUsers", (data) => {
            console.log("Received message event:", data);
            set({ onlineUsers: data.onlineUsers });
        });

        pusher.connection.bind("connected", () => {
            console.log("Successfully connected to Pusher");
            // Send connect request
            get().userConnect(authUser.id);
        });

        pusher.connection.bind("disconnected", () => {
            console.log("Disconnected from Pusher");
            // Send disconnect request
            get().userDisconnect(authUser.id);
        });

        window.addEventListener("beforeunload", () => {
            if (pusher) {
                get().userDisconnect(authUser.id);
            }
        });

        set({ pusher });
    },

    disconnectPusher: () => {
        const { pusher } = get();
        if (pusher) {
            pusher.disconnect();
            set({ pusher: null });
        }
    },

    checkAuth: async () => {
        try {
            const res = await axiosInstance.get("/api/auth/check");
            set({ authUser: res.data.user });
            get().initializePusher(); // Initialize Pusher after auth
            get().userConnect(res.data.user.id);
        } catch (error) {
            console.log(error);
            set({ authUser: null });
        } finally {
            set({ isCheckingAuth: false });
        }
    },

    signup: async (data) => {
        set({ loading: true, error: null });
        try {
            const response = await axiosInstance.post("/api/auth/signup", data);
            set({ authUser: response.data.user, error: null });
        } catch (error) {
            console.error("Full registration error:", error);
            set({
                error: error.response?.data?.message || "Registration failed",
                user: null,
            });
            throw error;
        } finally {
            set({ loading: false });
        }
    },
    login: async (data) => {
        set({ isSignUp: true });
        try {
            const res = await axiosInstance.post("/api/auth/signin", data);
            set({ authUser: res.data.user });
            get().userConnect(res.data.user.id);
            toast.success("Logged in successfully");
        } catch (error) {
            toast.error(error.response.data.message);
        } finally {
            set({ isSignUp: false });
        }
    },
    logout: async () => {
        try {
            await axiosInstance.post("/api/auth/signout");
            set({ authUser: null });
            toast.success("Logged out successfully");
            get().disconnectPusher(); // Disconnect Pusher on logout
        } catch (error) {
            toast.error(error.response.data.message);
        }
    },
    updateProfile: async (data) => {
        set({ isUpdatingProfile: true });
        try {
            const res = await axiosInstance.put(
                "/api/user/update-profile",
                data
            );
            set({ authUser: res.data.user });
            toast.success("Profile updated successfully");
        } catch (error) {
            toast.error(error.response.data.message);
        } finally {
            set({ isUpdatingProfile: false });
        }
    },

    getUsers: async () => {
        set({ isUsersLoading: true });
        try {
            const res = await axiosInstance.get("/api/user");
            set({ users: res.data.users });
            console.log(res.data.users);
        } catch (error) {
            toast.error(error.response.data.message);
        } finally {
            set({ isUsersLoading: false });
        }
    },
    getMessages: async (id) => {
        set({ isMessagesLoading: true });
        try {
            if (id) {
                const res = await axiosInstance.get(`/api/message/${id}`);
                set({ messages: res.data.messages });
            }
        } catch (error) {
            toast.error(error.response.data.message);
        } finally {
            set({ isMessagesLoading: false });
        }
    },
    sendMessage: async (messageData) => {
        const { selectedUser, messages } = get();
        try {
            const res = await axiosInstance.post(
                `/api/message/${selectedUser.id}`,
                messageData
            );
            set({ messages: [...messages, res.data.message] });
        } catch (error) {
            toast.error(error.response.data.message);
        }
    },
    setSelectedUser: (selectedUser) => set({ selectedUser }),
    userConnect: async (userId) => {
        try {
            await axiosInstance.get(`/api/auth/user/connect/${userId}`);
        } catch (error) {
            toast.error(error.response.data.message);
        }
    },
    userDisconnect: async (userId) => {
        try {
            await axiosInstance.get(`/api/auth/user/disconnect/${userId}`);
        } catch (error) {
            toast.error(error.response.data.message);
        }
    },
}));
