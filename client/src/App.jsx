import { BrowserRouter, Routes, Route } from "react-router-dom";

import { useAuthStore } from "./store/useAuthStore";
import { useEffect } from "react";
import { Loader } from "lucide-react";
import { Toaster } from "react-hot-toast";
import { useThemeStore } from "./store/useThemeStore";
import Header from "./components/Header";
import Home from "./pages/Home";
import Login from "./pages/Login";
import SignUp from "./pages/SignUp";
import Settings from "./pages/Settings";
import Profile from "./pages/Profile";
import SendNotification from "./pages/SendNotification";
import { GetNotification } from "./pages/GetNotification";

function App() {
    const { authUser, checkAuth, isCheckingAuth } = useAuthStore();
    const { theme } = useThemeStore();
    useEffect(() => {
        checkAuth();
    }, [checkAuth]);
    if (isCheckingAuth && !authUser) {
        return (
            <div className="flex justify-center items-center h-screen">
                <Loader className="size-10 animate-spin" />
            </div>
        );
    }
    return (
        <div data-theme={theme}>
            <BrowserRouter>
                <Header />
                <Routes>
                    <Route path="/" element={authUser ? <Home /> : <Login />} />
                    <Route
                        path="/signup"
                        element={!authUser ? <SignUp /> : <Home />}
                    />
                    <Route
                        path="/login"
                        element={!authUser ? <Login /> : <Home />}
                    />
                    <Route path="/settings" element={<Settings />} />
                    <Route
                        path="/profile"
                        element={authUser ? <Profile /> : <Login />}
                    />
                    <Route
                        path="/send-notification"
                        element={<SendNotification />}
                    />
                    <Route
                        path="/get-notification"
                        element={<GetNotification />}
                    />
                </Routes>
                <Toaster />
            </BrowserRouter>
        </div>
    );
}

export default App;
