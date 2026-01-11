import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import * as envoyService from './envoy.service';

export const getEnvoySettingHandler = asyncHandler(async (req: Request, res: Response) => {
    const { userId } = req.params;
    const setting = await envoyService.getEnvoySettingByUserId(Number(userId));
    res.status(200).json(setting);
}, 'Failed to get envoy setting');
