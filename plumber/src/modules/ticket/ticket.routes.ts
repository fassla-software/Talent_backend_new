import { Router } from 'express';
import ticketController from './ticket.controller';
import { createTicketValidation, updateTicketValidation, paramsValidator } from './ticket.validation';
import { authenticate } from '../../middlewares/auth.middleware';
import upload from '../../middlewares/upload.middleware';

const router = Router();

router.post('/', authenticate, upload.array('files', 10), createTicketValidation, ticketController.createTicket);
router.get('/all', authenticate, ticketController.getTickets);
router.get('/my-tickets', authenticate, ticketController.getMyTickets);
router.get('/:id', authenticate, paramsValidator, ticketController.getTicketById);
router.put('/:id', authenticate, upload.array('files', 10), paramsValidator, updateTicketValidation, ticketController.updateTicket);
router.delete('/:id', authenticate, paramsValidator, ticketController.deleteTicket);

export default router;
