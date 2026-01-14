import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import Trader from '../trader/trader.model';
import Plumber from '../plumber/plumber.model';

export enum ClientType {
    TRADER = 'TRADER',
    PLUMBER = 'PLUMBER',
}

class ClientStatusHistory extends Model {
    public id!: number;
    public client_id!: number;
    public client_type!: ClientType;
    public old_status?: string | null;
    public new_status!: string;
    public changed_at!: Date;
    public readonly createdAt!: Date;
    public readonly updatedAt!: Date;

    // Associations
    public trader?: Trader;
    public plumber?: Plumber;
}

ClientStatusHistory.init(
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true,
        },
        client_id: {
            type: DataTypes.INTEGER,
            allowNull: false,
        },
        client_type: {
            type: DataTypes.ENUM('TRADER', 'PLUMBER'),
            allowNull: false,
        },
        old_status: {
            type: DataTypes.STRING(50),
            allowNull: true,
        },
        new_status: {
            type: DataTypes.STRING(50),
            allowNull: false,
        },
        changed_at: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: DataTypes.NOW,
        },
    },
    {
        sequelize,
        tableName: 'client_status_history',
        timestamps: true,
        underscored: true,
    }
);

// Associations
ClientStatusHistory.belongsTo(Trader, {
    foreignKey: 'client_id',
    constraints: false,
    as: 'trader',
});

ClientStatusHistory.belongsTo(Plumber, {
    foreignKey: 'client_id',
    constraints: false,
    as: 'plumber',
});

export default ClientStatusHistory;
