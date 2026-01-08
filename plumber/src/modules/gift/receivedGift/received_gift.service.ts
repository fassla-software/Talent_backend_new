import { Sequelize } from 'sequelize';
import HttpError from '../../../utils/HttpError';
import { viewImages } from '../../../utils/imageUtils';
import Plumber from '../../plumber/plumber.model';
import Gift from '../gift.model';
import ReceivedGift from './received_gift.model';
import User from '../../user/user.model';
import sequelize from '../../../config/db';

export const createReceivedGift = async (giftData: { user_id: number; gift_id: number }) => {
  const { gift_id, user_id } = giftData;

  // Fetch the gift details
  const gift = await Gift.findByPk(gift_id);
  if (!gift) {
    throw new HttpError('Gift not found', 404);
  }

  // Check user points
  const user = await Plumber.findOne({ where: { user_id } });
  if (!user) {
    throw new HttpError('User not found', 404);
  }

  if (user.gift_points === undefined || user.gift_points < gift.points_required) {
    throw new HttpError('Insufficient points', 400);
  }

  // Deduct points and create the received gift record
  const transaction = await sequelize.transaction();
  try {
    // Deduct the points
    await Plumber.update(
      {
        gift_points: Sequelize.literal(`gift_points - ${gift.points_required}`),
      },
      {
        where: { user_id },
        transaction,
      }
    );

    // Create the received gift record with `Pending` status
    const receivedGift = await ReceivedGift.create(
      {
        ...giftData,
        status: 'Pending', // Default status when a gift is received
      },
      { transaction }
    );

    await transaction.commit(); // Commit transaction
    return receivedGift;
  } catch (error) {
    await transaction.rollback();
    throw error;
  }
};

export const getAllReceivedGifts = async (filter: { limit?: number; skip?: number }) => {
  const { limit = 10, skip = 0 } = filter;
  const gifts = await ReceivedGift.findAll({
    include: [
      {
        model: User,
        as: 'plumber',
        attributes: ['name'],
      },
      {
        model: Gift,
        as: 'gift',
      },
    ],
    attributes: ['id', 'user_id', 'gift_id', 'status', 'createdAt', 'updatedAt'], // ✅ Include `status`
    limit: Number(limit),
    offset: Number(skip),
  });

  // ✅ Map gifts to include correct image URLs
  return gifts.map(item => {
    if (item.gift && item.gift.image) {
      item.gift.image = viewImages(item.gift.image) as string;
    }
    return item;
  });
};  // ✅ No extra code after this
