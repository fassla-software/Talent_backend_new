import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const paramsValidator = [
    param('id').isInt().withMessage('trader id must be number'),
    handleValidationErrors,
    strict,
];

export const bulkDeleteValidation = [
    body('ids')
        .isArray({ min: 1 })
        .withMessage('IDs must be a non-empty array')
        .custom(ids => {
            if (!Array.isArray(ids)) {
                throw new Error('IDs must be an array');
            }
            for (const id of ids) {
                if (typeof id !== 'number' || !Number.isInteger(id)) {
                    throw new Error('Each ID must be an integer');
                }
            }
            return true;
        }),
    handleValidationErrors,
    strict,
];
