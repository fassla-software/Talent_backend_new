import { Router } from 'express';
import ticketController from './ticket.controller';
import { createTicketValidation, updateTicketValidation, paramsValidator } from './ticket.validation';
import { authenticate } from '../../middlewares/auth.middleware';
import upload from '../../middlewares/upload.middleware';

const router = Router();

router.post('/', authenticate, upload.array('files', 10), createTicketValidation, ticketController.createTicket);
router.get('/all', ticketController.getTickets);
router.get('/my-tickets', authenticate, ticketController.getMyTickets);
router.get('/:id', paramsValidator, ticketController.getTicketById);
router.put('/:id', paramsValidator, updateTicketValidation, ticketController.updateTicket);
router.delete('/:id', paramsValidator, ticketController.deleteTicket);

export default router;
