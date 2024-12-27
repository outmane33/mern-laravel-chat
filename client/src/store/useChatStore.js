import { create } from "zustand";
import { axiosInstance } from "../lib/axios";

export const useChatStore = create((set) => ({
    messages: [],
    setMessages: (messages) => {
        set({ messages });
    },
    sendMessage: async (message) => {
        try {
            // Get CSRF cookie first
            await axiosInstance.get("/sanctum/csrf-cookie");

            // Then make the actual request
            const response = await axiosInstance.post("/api/message", message);
            set((state) => ({
                messages: [...state.messages, response.data.message],
            }));
        } catch (error) {
            console.error("Error sending message:", error);
        }
    },
}));
