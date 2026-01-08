import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const addSegmentValidator = [
  body('description').isString().withMessage('description must be a string'),
  // body('minPoints').isInt({ min: 0 }).withMessage('min points must be a positive number'),
  body('maxPoints').isInt({ min: 1 }).withMessage('max points must be a positive number'),
  body('pointsValue').isFloat({ min: 0 }).withMessage('pointsValue must be a positive number'),
  handleValidationErrors,
  strict,
];

export const updateSegmentValidator = [
  param('id').isInt().withMessage('category id must be number'),
  body('description').isString().withMessage('description must be a string').optional(),
  body('minPoints').isInt({ min: 1 }).withMessage('min points must be a positive number').optional(),
  body('maxPoints').isInt({ min: 1 }).withMessage('max points must be a positive number').optional(),
  body('pointsValue').isFloat({ min: 0 }).withMessage('pointsValue must be a positive number').optional(),
  handleValidationErrors,
  strict,
];

export const paramsValidator = [
  param('id').isInt().withMessage('segment id must be number'),
  handleValidationErrors,
  strict,
];
