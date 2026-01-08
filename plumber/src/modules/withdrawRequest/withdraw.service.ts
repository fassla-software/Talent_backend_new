import HttpError from '../../utils/HttpError';
import { saveImages, viewImages } from '../../utils/imageUtils';
import Plumber from '../plumber/plumber.model';
import User from '../user/user.model';
import WithdrawRequest from './withdraw.model';

export const addWithdrawRequest = async (data: {
  user_id: number;
  payment_identifier: string;
  image: string;
  transaction_type: 'wallet' | 'bank' | 'meeza';
}) => {
  console.log({ data });
  const { user_id, payment_identifier, transaction_type, image } = data;
  const user = await Plumber.findOne({ where: { user_id: user_id } });
  if (!user) throw new HttpError('User not found', 404);

  if (
    user.instant_withdrawal === 0 ||
    user.instant_withdrawal === undefined ||
    user.withdraw_money == undefined ||
    user.withdraw_money == 0
  ) {
    throw new HttpError('Instant withdrawal is not enabled for this user', 400);
  }

  const existRequest = await WithdrawRequest.findOne({
    where: {
      requestor_id: user_id,
      status: 'pending',
    },
  });
  if (existRequest) {
    throw new HttpError('You have an active withdrawal request', 400);
  }

  const imageUrl = saveImages(image);
  const request = await WithdrawRequest.create({
    requestor_id: user_id,
    amount: user.withdraw_money,
    payment_identifier,
    image: imageUrl,
    transaction_type,
    status: 'pending',
    request_date: new Date(),
  });
  return request;
};

export const updateWithdrawRequest = async (
  id: string,
  user_id: string,
  data: {
    payment_identifier?: string;
    image: string;
    transaction_type?: 'wallet' | 'bank' | 'meeza';
  },
) => {
  const { payment_identifier, transaction_type, image } = data;
  // Fetch the withdraw request by ID
  const request = await WithdrawRequest.findByPk(id);
  if (!request) throw new HttpError('Withdraw request not found', 404);
  if (request.requestor_id !== Number(user_id)) {
    throw new HttpError('User is not authorized to update this withdrawal request', 403);
  }
  if (payment_identifier) request.payment_identifier = payment_identifier;

  if (transaction_type) request.transaction_type = transaction_type;

  if (image) {
    const imageUrl = saveImages(image);
    request.image = imageUrl as string;
  }

  await request.save();
  return request;
};

export const getWithdrawRequests = async (status?: string) => {
  console.log({ status });
  const requests = await WithdrawRequest.findAll({
    where: { status: status ? status : 'pending' },
    include: [
      {
        model: User,
        as: 'requestor',
        attributes: ['name', 'phone'],
      },
    ],
  });

  return requests.map(request => {
    return { ...request.toJSON(), image: request.image ? viewImages(request.image) : '' };
  });
};

export const getUserWithdrawRequests = async (id: string, status?: string) => {
  console.log({ id, status });
  const requests = await WithdrawRequest.findAll({
    where: { status: status ? status : 'pending', requestor_id: id },
    include: [
      {
        model: User,
        as: 'requestor',
        attributes: ['name', 'phone'],
      },
    ],
  });

  return requests.map(request => {
    return { ...request.toJSON(), image: request.image ? viewImages(request.image) : '' };
  });
};
