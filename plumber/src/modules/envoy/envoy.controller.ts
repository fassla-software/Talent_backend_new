import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import * as envoyService from './envoy.service';
import { Roles } from '../role/role.model';

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
