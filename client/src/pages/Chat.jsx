import { useEffect, useState } from "react";
import Pusher from "pusher-js";

export default function Chat() {
    const [onlineUsers, setOnlineUsers] = useState([]);

    useEffect(() => {
        // Initialize Pusher
        Pusher.logToConsole = true;
        const pusher = new Pusher("373dc50e2dde8a2f8161", {
            cluster: "eu",
        });

        // Subscribe to the 'online-users' channel
        const channel = pusher.subscribe("online-users");
        channel.bind("getOnlineUsers", function (data) {
            setOnlineUsers(data); // Update online users list
        });

        return () => {
            pusher.unsubscribe("online-users");
        };
    }, []);

    return (
        <div>
            <h3>Online Users:</h3>
            <ul>
                {onlineUsers.map((userId, index) => (
                    <li key={index}>{userId}</li>
                ))}
            </ul>
        </div>
    );
}
