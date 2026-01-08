import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const addGiftVal = [
  body('name').notEmpty().withMessage('name is required').isString().withMessage('name must be a string'),
  body('image').isString().withMessage('image must be a string'),
  body('points_required').isInt().withMessage('points_required must be integer'),
  handleValidationErrors,
  strict,
];

export const updateGiftVal = [
  param('id').isInt().withMessage('id must be number'),
  body('name').isString().withMessage('name must be a string').optional(),
  body('image').isString().withMessage('image must be a string').optional(),
  body('points_required').isInt().withMessage('points_required must be integer').optional(),
  handleValidationErrors,
  strict,
];

export const paramsValidator = [param('id').isInt().withMessage('id must be number'), handleValidationErrors, strict];
