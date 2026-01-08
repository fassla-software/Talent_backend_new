import HttpError from '../../utils/HttpError';
import { saveImages, viewImages } from '../../utils/imageUtils';
import Plumber from '../plumber/plumber.model';
import Gift from './gift.model';

export const createGift = async (giftData: { name: string; points_required: number; image: string }) => {
  giftData.image = saveImages(giftData.image) as string;
  const gift = await Gift.create(giftData);
  return {
    ...gift.toJSON(),
    image: viewImages(gift.image),
  };
};

export const getallGifts = async () => {
  const result = await Gift.findAndCountAll({
    order: [['points_required', 'ASC']],
  });

  const { rows, count } = result;
  return {
    count,
    gifts: rows.map(gift => ({
      ...gift.toJSON(),
      image: viewImages(gift.image),
    })),
  };
};

export const getGifts = async (userId: string) => {
  const user = await Plumber.findOne({ where: { user_id: userId } });
  if (!user) {
    throw new HttpError('User not found', 400);
  }
  const result = await Gift.findAndCountAll({
    order: [['points_required', 'ASC']],
  });

  const { rows, count } = result;
  return {
    count,
    user_points: user.gift_points,
    gifts: rows.map(gift => ({
      ...gift.toJSON(),
      image: viewImages(gift.image),
    })),
  };
};

export const getGiftById = async (id: number) => {
  const gift = await Gift.findByPk(id);
  if (!gift) {
    throw new HttpError(`Gift with ID ${id} not found`, 404);
  }
  return {
    ...gift.toJSON(),
    image: viewImages(gift.image),
  };
};

export const updateGift = async (
  id: string,
  updateData: { name?: string; points_required?: number; image?: string },
) => {
  const gift = await Gift.findByPk(id);
  if (!gift) {
    throw new HttpError(`Gift with ID ${id} not found`, 404);
  }
  if (updateData.image) updateData.image = saveImages(updateData.image) as string;
  await gift.update(updateData);
  return {
    ...gift.toJSON(),
    image: viewImages(gift.image),
  };
};

export const deleteGift = async (id: string) => {
  const gift = await Gift.findByPk(id);
  if (!gift) {
    throw new HttpError(`Gift with ID ${id} not found`, 404);
  }
  await gift.destroy();
  return;
};
