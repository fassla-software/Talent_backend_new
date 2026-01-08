import { body } from 'express-validator';
import { handleValidationErrors, strict } from '../../utils/base.validators';

export const sendMessageVal = [
  body('name').isString().withMessage('Name must be a string'),
  body('phone')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('Invalid phone number'),
  body('message').isString().withMessage('message must be string'),
  handleValidationErrors,
  strict,
];
