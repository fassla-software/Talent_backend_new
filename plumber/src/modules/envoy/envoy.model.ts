import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';

class EnvoySetting extends Model {
    public id!: number;
    public user_id!: number;
    public weight_sales!: number;
    public weight_visits!: number;
    public weight_retention_rate!: number;
    public weight_conversion_rate!: number;
    public target_sales!: number;
    public target_visits!: number;
    public target_retention_rate!: number;
    public target_conversion_rate!: number;
    public salary!: number;
    public incentives!: number;
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
        weight_sales: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        weight_visits: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        weight_retention_rate: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        weight_conversion_rate: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        target_sales: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        target_visits: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        target_retention_rate: {
            type: DataTypes.FLOAT,
            defaultValue: 0,
        },
        target_conversion_rate: {
            type: DataTypes.FLOAT,
            defaultValue: 0,
        },
        salary: {
            type: DataTypes.INTEGER,
            defaultValue: 0,
        },
        incentives: {
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
