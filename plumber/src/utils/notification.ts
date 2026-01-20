import axios from 'axios';
import { getConfig } from 'dotenv-handler';
import User from '../modules/user/user.model';
import DeviceKey from '../modules/user/device_key.model';

export async function sendPushNotification(userId: number, title: string, body: string) {
    try {
        const user = await User.findByPk(userId);
        if (!user) {
            console.log(`Notification skipped: User ${userId} not found.`);
            return;
        }

        // Get all device tokens for this user
        const deviceKeys = await DeviceKey.findAll({ where: { user_id: userId } });
        const tokens = deviceKeys.map(dk => dk.key);

        // Also include the main device_token from users table if not already in the list
        if (user.device_token && !tokens.includes(user.device_token)) {
            tokens.push(user.device_token);
        }

        if (tokens.length === 0) {
            console.log(`Notification skipped: User ${userId} has no device tokens.`);
            return;
        }

        let baseUrl = getConfig('BASE_URL');
        if (!baseUrl) {
            console.error('Notification skipped: BASE_URL is not configured.');
            return;
        }
        // Remove /plumber suffix if present to reach the Laravel API
        baseUrl = baseUrl.replace(/\/plumber\/?$/, '');
        const apiUrl = `${baseUrl}/api/v1/send-notification`;

        // Send to each token (Laravel API currently handles single token per request)
        for (const token of tokens) {
            try {
                const response = await axios.post(apiUrl, {
                    token: token,
                    title: title,
                    body: body,
                    user_id: userId,
                });
                console.log(`Notification sent to user ${userId} (token: ${token}):`, response.data);
            } catch (err: any) {
                console.error(`Failed to send notification to token ${token} for user ${userId}:`, err.message);
            }
        }
    } catch (error: any) {
        console.error(`Error in sendPushNotification for user ${userId}:`, error.message);
    }
}
