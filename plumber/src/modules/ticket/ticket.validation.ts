import { body, param } from 'express-validator';
import { TicketPriority, TicketStatus } from './ticket.model';
import { handleValidationErrors, strict } from '../../utils/base.validators';

export const paramsValidator = [
    param('id').isInt().withMessage('Ticket ID must be a number'),
    handleValidationErrors,
];

export const createTicketValidation = [
    body('client_phone').isString().notEmpty().withMessage('Client phone is required'),
    body('title').isString().notEmpty().withMessage('Title is required'),
    body('issue').isString().notEmpty().withMessage('Issue description is required'),
    body('priority')
        .optional()
        .isIn(Object.values(TicketPriority))
        .withMessage(`Priority must be one of: ${Object.values(TicketPriority).join(', ')}`),
    body('due_date').optional().isISO8601().toDate().withMessage('Due date must be a valid date'),
    handleValidationErrors,
    strict,
];

export const updateTicketValidation = [
    body('status')
        .optional()
        .isIn(Object.values(TicketStatus))
        .withMessage(`Status must be one of: ${Object.values(TicketStatus).join(', ')}`),
    body('priority')
        .optional()
        .isIn(Object.values(TicketPriority))
        .withMessage(`Priority must be one of: ${Object.values(TicketPriority).join(', ')}`),
    body('due_date').optional().isISO8601().toDate().withMessage('Due date must be a valid date'),
    body('note')
        .optional()
        .isString()
        .withMessage('Note must be a string')
        .custom((value, { req }) => {
            if (req.body.status === TicketStatus.CLOSED && !value) {
                throw new Error('Note is required when closing a ticket');
            }
            return true;
        }),
    body('close_reason')
        .optional()
        .isString()
        .withMessage('Close reason must be a string')
        .custom((value, { req }) => {
            if (req.body.status === TicketStatus.CLOSED && !value) {
                throw new Error('Close reason is required when closing a ticket');
            }
            return true;
        }),
    handleValidationErrors,
    strict,
];
