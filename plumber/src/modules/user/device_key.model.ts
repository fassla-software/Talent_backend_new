import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from './user.model';

class DeviceKey extends Model {
    public id!: number;
    public user_id!: number;
    public key!: string;
    public device_type!: string;
    public readonly created_at!: Date;
    public readonly updated_at!: Date;
}

DeviceKey.init(
    {
        id: {
            type: DataTypes.BIGINT.UNSIGNED,
            primaryKey: true,
            autoIncrement: true,
        },
        user_id: {
            type: DataTypes.BIGINT.UNSIGNED,
            allowNull: false,
            references: {
                model: User,
                key: 'id',
            },
            onDelete: 'CASCADE',
        },
        key: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        device_type: {
            type: DataTypes.STRING,
            allowNull: true,
            defaultValue: 'android',
        },
    },
    {
        sequelize,
        tableName: 'device_keys',
        underscored: true,
        timestamps: true,
    }
);

export default DeviceKey;
