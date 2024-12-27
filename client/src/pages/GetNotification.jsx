import { useEffect, useState } from "react";
import Pusher from "pusher-js";
import { useAuthStore } from "../store/useAuthStore";

export const GetNotification = () => {
    const [messages, setMessages] = useState([]);
    // const { authUser } = useAuthStore();

    // useEffect(() => {
    //     Pusher.logToConsole = true;

    //     const pusher = new Pusher("9a5027a440d42c7d94b5", {
    //         cluster: "ap3",
    //         forceTLS: true,
    //     });

    //     const channel = pusher.subscribe(`user${authUser?.id}`);

    //     // Log successful subscription
    //     channel.bind("pusher:subscription_succeeded", () => {
    //         console.log("Successfully subscribed to channel");
    //     });

    //     // Listen for your specific event
    //     channel.bind("message", (data) => {
    //         console.log("Received message event:", data);
    //         setMessages((prevMessages) => [...prevMessages, data]);
    //     });

    //     pusher.connection.bind("connected", () => {
    //         console.log("Successfully connected to Pusher");
    //     });

    //     return () => {
    //         channel.unbind_all();
    //         channel.unsubscribe();
    //         pusher.disconnect();
    //     };
    // }, [authUser]);

    return (
        <div>
            <h2>Notifications ({messages.length})</h2>
            {messages.map((msg, index) => (
                <div key={index}>{JSON.stringify(msg)}</div>
            ))}
        </div>
    );
};
