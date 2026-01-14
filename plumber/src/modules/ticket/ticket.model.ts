import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';

export enum TicketStatus {
    OPEN = 'OPEN',
    IN_PROGRESS = 'IN_PROGRESS',
    CLOSED = 'CLOSED',
}

export enum TicketPriority {
    HIGH = 'HIGH',
    AVERAGE = 'AVERAGE',
    LOW = 'LOW',
}

class Ticket extends Model {
    public id!: number;
    public code!: string;
    public client_id!: number;
    public inspector_id!: number;
    public status!: TicketStatus;
    public title!: string;
    public issue!: string;
    public files?: string[] | null;
    public note?: string | null;
    public close_reason?: string | null;
    public priority!: TicketPriority;
    public due_date?: Date | null;
    public readonly created_at!: Date;
    public readonly updated_at!: Date;

    // Associations
    public readonly inspector?: User;
    public readonly client?: User;
}

Ticket.init(
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true,
        },
        code: {
            type: DataTypes.STRING,
            allowNull: false,
            unique: true,
        },
        client_id: {
            type: DataTypes.BIGINT,
            allowNull: false,
            references: {
                model: 'users',
                key: 'id',
            },
        },
        inspector_id: {
            type: DataTypes.BIGINT,
            allowNull: false,
            references: {
                model: 'users',
                key: 'id',
            },
        },
        status: {
            type: DataTypes.ENUM(...Object.values(TicketStatus)),
            allowNull: false,
            defaultValue: TicketStatus.OPEN,
        },
        title: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        issue: {
            type: DataTypes.TEXT,
            allowNull: false,
        },
        files: {
            type: DataTypes.JSON,
            allowNull: true,
            get() {
                const rawValue = this.getDataValue('files');
                return rawValue ? (typeof rawValue === 'string' ? JSON.parse(rawValue) : rawValue) : null;
            },
            set(value) {
                this.setDataValue('files', value ? JSON.stringify(value) : null);
            },
        },
        note: {
            type: DataTypes.TEXT,
            allowNull: true,
        },
        close_reason: {
            type: DataTypes.TEXT,
            allowNull: true,
        },
        priority: {
            type: DataTypes.ENUM(...Object.values(TicketPriority)),
            allowNull: false,
            defaultValue: TicketPriority.AVERAGE,
        },
        due_date: {
            type: DataTypes.DATE,
            allowNull: true,
        },
    },
    {
        sequelize,
        tableName: 'tickets',
        timestamps: true,
        underscored: true,
    },
);

Ticket.belongsTo(User, {
    foreignKey: 'inspector_id',
    as: 'inspector',
});

Ticket.belongsTo(User, {
    foreignKey: 'client_id',
    as: 'client',
});

User.hasMany(Ticket, {
    foreignKey: 'inspector_id',
    as: 'tickets',
});

User.hasMany(Ticket, {
    foreignKey: 'client_id',
    as: 'client_tickets',
});

export default Ticket;
