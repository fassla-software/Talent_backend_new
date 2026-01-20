import { Response, NextFunction } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import ticketService from './ticket.service';
import { AuthenticatedRequest } from '../../@types/express';
import { saveImages } from '../../utils/imageUtils';

class TicketController {
    createTicket = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
        if (!req.user) {
            throw new Error('Unauthorized');
        }
        const inspector_id = parseInt(req.user.id);
        const files = req.files as Express.Multer.File[];

        const data = {
            ...req.body,
            files: files && files.length > 0 ? files.map(file => file.filename) : undefined,
        };

        const ticket = await ticketService.createTicket(data, inspector_id);
        res.status(201).json({ message: 'Ticket created successfully', ticket });
    }, 'Failed to create ticket');

    getTickets = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
        const result = await ticketService.getAllTickets(req.query);
        res.status(200).json(result);
    }, 'Failed to get tickets');

    getMyTickets = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
        const inspectorId = parseInt(req.user!.id);
        const tickets = await ticketService.getTicketsByInspector(inspectorId, req.query);
        res.status(200).json(tickets);
    }, 'Failed to get my tickets');

    getTicketById = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
        const ticket = await ticketService.getTicketById(parseInt(req.params.id));
        res.status(200).json(ticket);
    }, 'Failed to get ticket');

    updateTicket = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
        const id = parseInt(req.params.id);
        const ticket = await ticketService.updateTicket(id, req.body);
        res.status(200).json({ message: 'Ticket updated successfully', ticket });
    }, 'Failed to update ticket');

    deleteTicket = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
        const result = await ticketService.deleteTicket(parseInt(req.params.id));
        res.status(200).json(result);
    }, 'Failed to delete ticket');
}

export default new TicketController();
