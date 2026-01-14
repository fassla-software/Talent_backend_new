import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const createAwardVal = [
    body('title').notEmpty().withMessage('title is required').isString().withMessage('title must be a string'),
    body('description').optional().isString().withMessage('description must be a string'),
    handleValidationErrors,
    strict,
];

export const updateAwardVal = [
    param('id').isInt().withMessage('id must be number'),
    body('title').optional().isString().withMessage('title must be a string'),
    body('description').optional().isString().withMessage('description must be a string'),
    handleValidationErrors,
    strict,
];

export const assignAwardVal = [
    body('envoy_id').notEmpty().withMessage('envoy_id is required').isInt().withMessage('envoy_id must be integer'),
    body('award_id').notEmpty().withMessage('award_id is required').isInt().withMessage('award_id must be integer'),
    body('reason').optional().isString().withMessage('reason must be a string'),
    handleValidationErrors,
    strict,
];

export const updateEnvoyAwardVal = [
    param('id').isInt().withMessage('id must be number'),
    body('envoy_id').optional().isInt().withMessage('envoy_id must be integer'),
    body('award_id').optional().isInt().withMessage('award_id must be integer'),
    body('reason').optional().isString().withMessage('reason must be a string'),
    handleValidationErrors,
    strict,
];

export const paramsValidator = [param('id').isInt().withMessage('id must be number'), handleValidationErrors, strict];
