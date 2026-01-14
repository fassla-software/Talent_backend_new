import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import { AuthenticatedRequest } from '../../@types/express';
import {
    getTraderById,
    updateTrader,
    updateProfile,
    getTraders,
    bulkDeleteTraders,
    acceptTrader,
    rejectTrader,
    getProfile,
    searchTraders
} from './trader.service';
import EnvoySetting from '../envoy/envoy.model';

export const getTradersHandler = asyncHandler(async (req: Request, res: Response) => {
    const traders = await getTraders();
    res.status(200).json(traders);
}, 'Failed to get traders');

export const getTraderByIdHandler = asyncHandler(async (req: Request, res: Response) => {
    const { id } = req.params;
    const trader = await getTraderById(Number(id));
    res.status(200).json({ trader });
}, 'Failed to get trader');

export const updateTraderHandler = asyncHandler(async (req: Request, res: Response) => {
    const { id } = req.params;
    const data = req.body;
    await updateTrader(id, data);
    res.status(200).json({ message: 'Trader updated successfully' });
}, 'Failed to update trader');

export const bulkDeleteTradersHandler = asyncHandler(async (req: Request, res: Response) => {
    const { ids } = req.body;
    const result = await bulkDeleteTraders(ids);
    res.status(200).json({ message: 'Traders deleted successfully', deletedCount: result.deletedCount });
}, 'Failed to delete traders');

export const acceptTraderHandler = asyncHandler(async (req: Request, res: Response) => {
    const { id } = req.params;
    await acceptTrader(Number(id));
    res.status(200).json({ message: 'Trader accepted successfully' });
}, 'Failed to accept trader');

export const rejectTraderHandler = asyncHandler(async (req: Request, res: Response) => {
    const { id } = req.params;
    await rejectTrader(Number(id));
    res.status(200).json({ message: 'Trader rejected successfully' });
}, 'Failed to reject trader');

export const getProfileHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
    const userId = req.user!.id;
    const user = await getProfile(userId);
    res.status(200).json({ user });
}, 'Failed to get profile');

export const updateProfileHandler = asyncHandler(async (req: Request, res: Response) => {
    const authHeader = req.headers.authorization;
    if (!authHeader) {
        throw new Error('Authorization header missing');
    }
    const token = authHeader.split(' ')[1];
    const data = req.body;
    await updateProfile(token, data);
    res.status(200).json({ message: 'Profile updated successfully' });
}, 'Failed to update profile');

export const searchTradersHandler = asyncHandler(async (req: any, res: Response) => {
    const { name, phone } = req.query;

    if (!name && !phone) {
        return res.status(400).json({ message: 'Either name or phone parameter is required' });
    }

    let city: string | undefined;

    // Middleware guarantees user is authenticated and is an envoy
    const userId = req.user.id;
    const envoySetting = await EnvoySetting.findOne({ where: { user_id: userId } });
    if (envoySetting && envoySetting.region) {
        city = envoySetting.region;
    }

    const nameStr = typeof name === 'string' ? name : undefined;
    const phoneStr = typeof phone === 'string' ? phone : undefined;

    const result = await searchTraders(nameStr, phoneStr, city);
    res.status(200).json(result);
}, 'Failed to search traders');
