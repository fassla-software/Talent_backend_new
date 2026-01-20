import { TicketPriority } from '../ticket.model';

export interface CreateTicketDto {
    client_phone: string;
    title: string;
    issue: string;
    files?: string[];
    priority?: TicketPriority;
    due_date?: Date;
}
