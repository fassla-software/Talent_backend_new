import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import * as envoyService from './envoy.service';
import { Roles } from '../role/role.model';
import { getEnvoyStatistics, TimePeriodType } from './envoy-stats.service';

export const getEnvoySettingHandler = asyncHandler(async (req: Request, res: Response) => {
    const { userId } = req.params;
    const setting = await envoyService.getEnvoySettingByUserId(Number(userId));
    res.status(200).json(setting);
}, 'Failed to get envoy setting');

export const registerUserByEnvoyHandler = asyncHandler(async (req: any, res: Response) => {
    const inspectorId = req.user.id; // Get envoy ID from authenticated user
    const { role, ...userData } = req.body;

    // Handle uploaded files (nationality images)
    const files = req.files as Express.Multer.File[];
    if (files && files.length > 0) {
        if (files[0]) userData.nationality_image1 = files[0].filename;
        if (files[1]) userData.nationality_image2 = files[1].filename;
    }


    // Convert role string to Roles enum
    const userRole = role === 'plumber' ? Roles.PLUMBER : Roles.TRADER;

    const result = await envoyService.registerUserByEnvoy(inspectorId, userData, userRole);

    res.status(201).json({
        message: 'User registered successfully by envoy',
        data: result,
    });
}, 'Failed to register user by envoy');

export const getEnvoyStatisticsHandler = asyncHandler(
    async (req: any, res: Response) => {
        const inspectorId = parseInt(req.user.id);
        const period = req.query.period as TimePeriodType;
        const dateStr = req.query.date as string | undefined;

        // Validate period
        if (!['week', 'month', 'quarter', 'year'].includes(period)) {
            return res.status(400).json({
                message: 'Invalid period. Must be week, month, quarter, or year',
            });
        }

        const date = dateStr ? new Date(dateStr) : undefined;
        const statistics = await getEnvoyStatistics(inspectorId, period, date);

        res.status(200).json({
            message: 'Statistics retrieved successfully',
            data: statistics,
        });
    },
    'Failed to get statistics'
);

export const getAdminEnvoyStatisticsHandler = asyncHandler(
    async (req: Request, res: Response) => {
        const { envoy_id, period, date: dateStr } = req.body;

        if (!envoy_id) {
            return res.status(400).json({
                message: 'envoy_id is required',
            });
        }

        const periodType = (period as TimePeriodType) || 'week';

        // Validate period
        if (!['week', 'month', 'quarter', 'year'].includes(periodType)) {
            return res.status(400).json({
                message: 'Invalid period. Must be week, month, quarter, or year',
            });
        }

        const date = dateStr ? new Date(dateStr) : undefined;
        const statistics = await getEnvoyStatistics(Number(envoy_id), periodType, date);

        res.status(200).json({
            message: 'Statistics retrieved successfully',
            data: statistics,
        });
    },
    'Failed to get admin statistics'
);
export const getNotificationsHandler = asyncHandler(async (req: any, res: Response) => {
    const userId = req.user.id;
    const notifications = await envoyService.getNotifications(Number(userId));
    res.status(200).json({
        message: 'Notifications retrieved successfully',
        data: notifications,
    });
}, 'Failed to get notifications');

export const createNoteHandler = asyncHandler(async (req: any, res: Response) => {
    const envoyId = req.user.id;
    const { client_id, content } = req.body;

    const note = await envoyService.createNote(Number(envoyId), Number(client_id), content);

    res.status(201).json({
        message: 'Note created successfully',
        data: note,
    });
}, 'Failed to create note');

export const getEnvoyClientsHandler = asyncHandler(async (req: any, res: Response) => {
    const envoyId = req.user.id;
    const { name, phone } = req.query;
    const clients = await envoyService.getEnvoyClients(Number(envoyId), name as string, phone as string);

    res.status(200).json({
        message: 'Clients retrieved successfully',
        data: clients,
    });
}, 'Failed to get envoy clients');
