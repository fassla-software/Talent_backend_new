import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';
import { TransactionType } from './withdraw.model';

export const paramsValidator = [
  param('id').isInt().withMessage('Request ID must be a number'),
  handleValidationErrors,
  strict,
];

export const addRequestValidator = [
  body('payment_identifier')
    .isString()
    .withMessage('Payment identifier must be a string')
    .isLength({ min: 1 })
    .withMessage('Payment identifier cannot be empty'),
  body('transaction_type')
    .isIn(Object.values(TransactionType))
    .withMessage(`Transaction type must be one of: ${Object.values(TransactionType).join(', ')}`),
  body('image')
    .custom(value => {
      if (value === '') return true;
      try {
        new URL(value);
        return true;
      } catch (err) {
        return false;
      }
    })
    .withMessage('image must be url')
    .optional(),
  handleValidationErrors,
  strict,
];

export const updateRequestValidator = [
  param('id').isInt().withMessage('Request ID must be a number'),
  body('payment_identifier')
    .isString()
    .withMessage('Payment identifier must be a string')
    .isLength({ min: 1 })
    .withMessage('Payment identifier cannot be empty')
    .optional(),
  body('image')
    .custom(value => {
      if (value === '') return true;
      try {
        new URL(value);
        return true;
      } catch (err) {
        return false;
      }
    })
    .withMessage('image must be url')
    .optional(),
  body('transaction_type')
    .isIn(Object.values(TransactionType))
    .withMessage(`Transaction type must be one of: ${Object.values(TransactionType).join(', ')}`)
    .optional(),
  handleValidationErrors,
  strict,
];
