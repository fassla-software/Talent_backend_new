/* eslint-disable @typescript-eslint/no-explicit-any */
import jwt from 'jsonwebtoken';
import Trader, { TraderStatus } from './trader.model';
import User from '../user/user.model';
import HttpError from '../../utils/HttpError';
import { IUpdatePlumber } from '../plumber/dto/update-plumber.dto';
import { saveImages, viewImages } from '../../utils/imageUtils';
import { Sequelize } from 'sequelize';
import { PlumberStatus } from '../plumber/plumber.model';

export const getTraderById = async (userId: number) => {
    const user = await User.findOne({ where: { id: userId } });
    if (!user) {
        throw new HttpError('User not Found', 404);
    }

    const trader = await Trader.findOne({
        where: {
            user_id: user.id,
        },
    });
    if (!trader) {
        throw new HttpError('Trader not Found', 404);
    }
    const existTrader = trader.toJSON();
    return {
        ...existTrader,

        image: existTrader.image ? (viewImages(existTrader.image) as string) : '',
        nationality_image1: existTrader?.nationality_image1 ? (viewImages(existTrader.nationality_image1) as string) : '',
        nationality_image2: existTrader?.nationality_image2 ? (viewImages(existTrader.nationality_image2) as string) : '',
    };
};

export const updateTrader = async (userId: string, newTrader: IUpdatePlumber) => {
    const { name, phone, city, area, image } = newTrader;

    const user = await User.findOne({
        where: {
            id: userId,
        },
    });

    if (!user) throw new HttpError('User not found', 404);

    if (name) user.name = name;
    if (phone) user.phone = phone;
    await user.save();

    // Update fields on the Trader model
    if (city || image || area) {
        const trader = await Trader.findOne({ where: { user_id: userId } });
        if (!trader) {
            throw new HttpError('Trader not found', 404);
        }

        if (city) trader.city = city;
        if (area) trader.area = area;
        if (image) trader.image = saveImages(image) as string;
        await trader.save();
    }

    return;
};

export const getTraders = async () => {
    const result = await Trader.findAndCountAll({
        include: [
            {
                model: User,
                as: 'user',
                required: true
            }

        ],

    });

    const { rows, count } = result;

    return {
        total_trader: count,
        traders: rows,
    };
};

export const bulkDeleteTraders = async (ids: number[]) => {
    // Check if all traders exist
    const existingTraders = await Trader.findAll({
        where: { id: ids },
    });

    if (existingTraders.length !== ids.length) {
        throw new HttpError('One or more traders not found', 404);
    }

    // Get user_ids from traders
    const userIds = existingTraders.map(trader => trader.user_id);

    // Delete traders first
    await Trader.destroy({
        where: { id: ids },
    });

    // Delete associated users
    const deletedUserCount = await User.destroy({
        where: { id: userIds },
    });

    return { deletedCount: deletedUserCount };
};

export const acceptTrader = async (userId: number) => {
    const trader = await Trader.findOne({
        where: { user_id: userId },
    });
    if (!trader) {
        throw new HttpError('Trader not found', 404);
    }
    const traderUser = await User.findOne({
        where: { id: userId },
    });
    if (!traderUser) {
        throw new HttpError('User not found', 404);
    }
    traderUser.status = PlumberStatus.APPROVED;
    await traderUser.save();
    trader.is_verified = true;
    await trader.save();
};

export const rejectTrader = async (userId: number) => {
    const trader = await Trader.findOne({
        where: { user_id: userId },
    });
    if (!trader) {
        throw new HttpError('Trader not found', 404);
    }
    const traderUser = await User.findOne({
        where: { id: userId },
    });
    if (!traderUser) {
        throw new HttpError('User not found', 404);
    }
    traderUser.status = PlumberStatus.REJECTED;
    await traderUser.save();
    trader.is_verified = false;
    await trader.save();
};

export const getProfile = async (id: string) => {
    const user = await User.findByPk(id, {
        attributes: ['id', 'name', 'phone', 'refer_code'],
    });
    if (!user) {
        throw new HttpError('User not found', 404);
    }

    const trader = await Trader.findOne({
        where: { user_id: id },
        attributes: {
            exclude: ['nationality_image1', 'nationality_image2', 'otp', 'expiration_date'],
        },
    });

    if (!trader) {
        throw new HttpError('Trader not found', 404);
    }

    return {
        refer_code: user.refer_code,
        ...user.toJSON(),
        role: 'trader',
        ...trader.toJSON(),
        image: trader.image ? (viewImages(trader.image) as string) : '',
    };
};

export const updateProfile = async (token: string, newTrader: IUpdatePlumber) => {
    const payload = jwt.verify(token, process.env.KEY!) as { id: string; role: string };
    const userId = payload.id;

    return await updateTrader(userId, newTrader);
};
