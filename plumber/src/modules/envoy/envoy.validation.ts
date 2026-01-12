import { body } from 'express-validator';
import { handleValidationErrors, strict } from '../../utils/base.validators';

export const registerUserByEnvoyValidation = [
    body('name').notEmpty().withMessage('Name is required'),
    body('password').isLength({ min: 6 }).withMessage('Password must be at least 6 characters'),
    body('phone').notEmpty().withMessage('Phone is required'),
    body('city').notEmpty().withMessage('City is required'),
    body('area').notEmpty().withMessage('Area is required'),
    body('role')
        .notEmpty()
        .withMessage('Role is required')
        .isIn(['plumber', 'trader'])
        .withMessage('Role must be either plumber or trader'),
    body('referralCode').optional().isString(),
    body('nationality_id').optional().isString(),
    body('nationality_image1').optional().isString(),
    body('nationality_image2').optional().isString(),
    handleValidationErrors,
    strict,
];
