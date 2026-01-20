import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';

export enum TraderStatus {
    PENDING = 'PENDING',
    APPROVED = 'APPROVED',
    REJECTED = 'REJECTED',
}

export enum TraderActivityStatus {
    ACTIVE = 'ACTIVE',           // نشط
    INACTIVE = 'INACTIVE',       // غير نشط
    DORMANT = 'DORMANT',         // خامل
    PENDING = 'PENDING',         // محتمل
}
interface TraderAttributes {
    id: number;
    user_id: number;
    inspector_id?: number | null;
    status: TraderActivityStatus;
    city: string;
    area: string;
    nationality_id?: string;
    nationality_image1?: string;
    nationality_image2?: string;
    image?: string;
    is_verified: boolean;
    otp: string | null;
    expiration_date: Date | null;
    instant_withdrawal: number;
    withdraw_money: number;
    points: number;
    latitude?: number | null;
    longitude?: number | null;
    createdAt: Date;
    updatedAt: Date;
    user?: User;
    inspector?: User;
}

interface TraderCreationAttributes
    extends Optional<
        TraderAttributes,
        | 'id'
        | 'createdAt'
        | 'updatedAt'
        | 'is_verified'
        | 'expiration_date'
        | 'otp'
        | 'points'
        | 'instant_withdrawal'
        | 'image'
        | 'withdraw_money'
        | 'inspector_id'
        | 'status'
    > { }

class Trader extends Model<TraderAttributes, TraderCreationAttributes> {
    public id!: number;
    public user_id!: number;
    public inspector_id?: number | null;
    public city!: string;
    public area!: string;
    public nationality_id?: string;
    public nationality_image1?: string;
    public nationality_image2?: string;
    public image?: string;
    public is_verified!: boolean;
    public status!: TraderActivityStatus;
    public otp?: string | null;
    public expiration_date?: Date | null;
    public createdAt!: Date;
    public updatedAt!: Date;
    public instant_withdrawal?: number;
    public withdraw_money?: number;
    public points?: number;
    public latitude?: number | null;
    public longitude?: number | null;

    // Define the association explicitly
    public user?: User;
    public inspector?: User;
}

// Initialize the model
Trader.init(
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true,
        },
        user_id: {
            type: DataTypes.INTEGER,
            allowNull: false,
            references: {
                model: 'users',
                key: 'id',
            },
            onDelete: 'CASCADE',
        },
        inspector_id: {
            type: DataTypes.BIGINT.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'id',
            },
            onDelete: 'SET NULL',
            onUpdate: 'CASCADE',
        },
        status: {
            type: DataTypes.ENUM(...Object.values(TraderActivityStatus)),
            allowNull: false,
            defaultValue: TraderActivityStatus.PENDING,
        },
        city: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        area: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        nationality_id: {
            type: DataTypes.STRING,
            allowNull: true,
        },
        nationality_image1: {
            type: DataTypes.STRING,
            allowNull: true,
        },
        nationality_image2: {
            type: DataTypes.STRING,
            allowNull: true,
        },
        image: {
            type: DataTypes.STRING,
            allowNull: true,
        },
        is_verified: {
            type: DataTypes.BOOLEAN,
            defaultValue: false,
            allowNull: false,
        },
        otp: {
            type: DataTypes.STRING,
            allowNull: true,
        },
        expiration_date: {
            type: DataTypes.DATE,
            allowNull: true,
        },
        instant_withdrawal: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0,
        },
        withdraw_money: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0,
        },
        points: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0,
        },
        latitude: {
            type: DataTypes.DECIMAL(10, 8),
            allowNull: true,
            defaultValue: null,
        },
        longitude: {
            type: DataTypes.DECIMAL(11, 8),
            allowNull: true,
            defaultValue: null,
        },
        createdAt: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: DataTypes.NOW,
        },
        updatedAt: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: DataTypes.NOW,
        },
    },
    {
        sequelize,
        tableName: 'traders',
        timestamps: true,
        underscored: true,
    },
);

Trader.belongsTo(User, { foreignKey: 'user_id', as: 'user' });
Trader.belongsTo(User, { foreignKey: 'inspector_id', as: 'inspector' });
User.hasMany(Trader, { foreignKey: 'user_id', as: 'traders' });

export default Trader;
