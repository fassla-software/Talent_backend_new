import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from './user.model';

class NotificationUnique extends Model {
    public id!: number;
    public user_id!: number | null;
    public title!: string;
    public body!: string;
    public device_token!: string | null;
    public type!: string | null;
    public readonly created_at!: Date;
    public readonly updated_at!: Date;
}

NotificationUnique.init(
    {
        id: {
            type: DataTypes.BIGINT.UNSIGNED,
            primaryKey: true,
            autoIncrement: true,
        },
        user_id: {
            type: DataTypes.BIGINT.UNSIGNED,
            allowNull: true,
            references: {
                model: User,
                key: 'id',
            },
            onDelete: 'SET NULL',
        },
        title: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        body: {
            type: DataTypes.TEXT,
            allowNull: false,
        },
        device_token: {
            type: DataTypes.STRING,
            allowNull: true,
        },
        type: {
            type: DataTypes.STRING,
            allowNull: true,
        },
    },
    {
        sequelize,
        tableName: 'notification_unique',
        underscored: true,
        timestamps: true,
    }
);

export default NotificationUnique;
