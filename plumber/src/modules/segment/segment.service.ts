import { Op } from 'sequelize';
import HttpError from '../../utils/HttpError';
import Plumber from '../plumber/plumber.model';
import Segment from './segment.model';

export const addSegment = async (data: {
  description: string;
  minPoints: number;
  maxPoints: number;
  pointsValue: number;
}) => {
  const { description, maxPoints, pointsValue } = data;
 const lastSegment = await Segment.findOne({
    order: [['maxPoints', 'DESC']],
  });

  const segment = await Segment.create({
    description,
    minPoints: lastSegment?.maxPoints ?? 1,
    maxPoints,
    pointsValue,
  });
  return segment;
};

export const getSegments = async () => {
  const segments = await Segment.findAll({});
  return segments;
};

export const calcUserPoints = async (id: string) => {
  const plumber = await Plumber.findOne({
    where: {
      user_id: id,
    },
  });
  if (!plumber) {
    return;
  }
  console.log({ plumber: plumber?.toJSON() });

  const segment = await Segment.findOne({
    where: {
      minPoints: { [Op.lte]: plumber.fixed_points },
      maxPoints: { [Op.gte]: plumber.fixed_points },
    },
  });
  console.log({ segment: segment?.toJSON() });
  if (!segment) {
    return;
  }
  const total_value = segment.pointsValue * (plumber.fixed_points ?? 0);
  return { fixed_points: plumber.fixed_points, total_value };
};

export const updateSegment = async (
  id: string,
  {
    description,
    minPoints,
    maxPoints,
    pointsValue,
  }: { description?: string; minPoints?: number; maxPoints?: number; pointsValue?: number },
) => {
  const segment = await Segment.findByPk(id);

  if (!segment) {
    throw new HttpError('segment not found', 404);
  }
  segment.description = description || segment.description;
  segment.minPoints = minPoints || segment.minPoints;
  segment.maxPoints = maxPoints || segment.maxPoints;
  segment.pointsValue = pointsValue || segment.pointsValue;
  await segment.save();
  return segment;
};

export const deleteSegment = async (id: string) => {
  const segment = await Segment.findByPk(id);
  if (!segment) {
    throw new HttpError('segment not found', 404);
  }
  await segment.destroy();
  return segment;
};
