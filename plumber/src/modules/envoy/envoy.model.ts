import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';

class EnvoySetting extends Model {
    public id!: number;
    public user_id!: number;
    public weight!: number;
    public target!: number;
    public salary!: number;
    public region!: string | null;
    public readonly createdAt!: Date;
    public readonly updatedAt!: Date;
}

EnvoySetting.init(
    {
        id: {
            type: DataTypes.BIGINT.UNSIGNED,
            primaryKey: true,
            autoIncrement: true,
        },
        user_id: {
            type: DataTypes.BIGINT.UNSIGNED,
            allowNull: false,
            unique: true,
            references: {
                model: User,
                key: 'id',
            },
            onDelete: 'CASCADE',
        },
        weight: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        target: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        salary: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        region: {
            type: DataTypes.TEXT,
            allowNull: true,
        },
    },
    {
        sequelize,
        tableName: 'envoy_settings',
        timestamps: true,
        underscored: true,
    }
);

// Associations
EnvoySetting.belongsTo(User, { foreignKey: 'user_id', as: 'user' });
User.hasOne(EnvoySetting, { foreignKey: 'user_id', as: 'envoySetting' });

export default EnvoySetting;
