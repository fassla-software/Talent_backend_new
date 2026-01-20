import { Op } from 'sequelize';
import Ticket, { TicketStatus } from './ticket.model';
import { CreateTicketDto } from './dto/create-ticket.dto';
import User from '../user/user.model';
import HttpError from '../../utils/HttpError';
import { saveImages, viewImages } from '../../utils/imageUtils';

class TicketService {
    async createTicket(data: CreateTicketDto, inspector_id: number) {
        const client = await User.findOne({
            where: {
                phone: {
                    [Op.like]: `%${data.client_phone}%`,
                },
            },
        });
        if (!client) {
            throw new HttpError('Client not found', 404);
        }

        const year = new Date().getFullYear();
        const lastTicket = await Ticket.findOne({ order: [['id', 'DESC']] });
        const nextId = lastTicket ? lastTicket.id + 1 : 1;
        const code = `TKT-${year}-${nextId.toString().padStart(3, '0')}`;

        const ticket = await Ticket.create({
            ...data,
            client_id: client.id,
            code,
            inspector_id,
            files: data.files ? saveImages(data.files) : null,
            status: TicketStatus.OPEN,
        });

        return this.formatTicketResponse(ticket);
    }

    async getAllTickets(query: any = {}) {
        const page = parseInt(query.page) || 1;
        const limit = parseInt(query.limit) || 20;
        const offset = (page - 1) * limit;

        const where: any = {};
        if (query.client_id) where.client_id = query.client_id;
        if (query.inspector_id) where.inspector_id = query.inspector_id;

        const { count, rows: tickets } = await Ticket.findAndCountAll({
            where,
            include: [
                { model: User, as: 'inspector', attributes: ['id', 'name', 'phone'] },
                { model: User, as: 'client', attributes: ['id', 'name', 'phone'] },
            ],
            order: [['created_at', 'DESC']],
            limit,
            offset,
        });

        return {
            tickets: tickets.map(ticket => this.formatTicketResponse(ticket)),
            pagination: {
                total: count,
                page,
                limit,
                totalPages: Math.ceil(count / limit),
            },
        };
    }

    async getTicketsByInspector(inspectorId: number, filters: any = {}) {
        const tickets = await Ticket.findAll({
            where: { ...filters, inspector_id: inspectorId },
            include: [
                { model: User, as: 'inspector', attributes: ['id', 'name', 'phone'] },
                { model: User, as: 'client', attributes: ['id', 'name', 'phone'] },
            ],
            order: [['created_at', 'DESC']],
        });

        return tickets.map(ticket => this.formatTicketResponse(ticket));
    }

    async getTicketById(id: number) {
        const ticket = await Ticket.findByPk(id, {
            include: [
                { model: User, as: 'inspector', attributes: ['id', 'name', 'phone'] },
                { model: User, as: 'client', attributes: ['id', 'name', 'phone'] },
            ],
        });

        if (!ticket) {
            throw new HttpError('Ticket not found', 404);
        }

        return this.formatTicketResponse(ticket);
    }

    async updateTicket(id: number, data: any) {
        const ticket = await Ticket.findByPk(id);
        if (!ticket) {
            throw new HttpError('Ticket not found', 404);
        }

        if (data.status === TicketStatus.CLOSED) {
            if (!data.note || !data.close_reason) {
                throw new HttpError('Note and close reason are required when closing a ticket', 400);
            }
        }

        if (data.files) {
            data.files = saveImages(data.files);
        }

        ticket.set(data);
        await ticket.save();
        await ticket.reload({
            include: [
                { model: User, as: 'inspector', attributes: ['id', 'name', 'phone'] },
                { model: User, as: 'client', attributes: ['id', 'name', 'phone'] },
            ],
        });

        return this.formatTicketResponse(ticket);
    }

    async deleteTicket(id: number) {
        const ticket = await Ticket.findByPk(id);
        if (!ticket) {
            throw new HttpError('Ticket not found', 404);
        }

        await ticket.destroy();
        return { message: 'Ticket deleted successfully' };
    }

    private formatTicketResponse(ticket: any) {
        const ticketObj = ticket.toJSON();
        return {
            ...ticketObj,
            files: ticketObj.files ? viewImages(ticketObj.files) : [],
        };
    }
}

export default new TicketService();
