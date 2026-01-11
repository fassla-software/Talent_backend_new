import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';
import Award from './award.model';

interface EnvoyAwardAttributes {
    id: number;
    envoy_id: number;
    award_id: number;
    reason: string | null;
    created_at?: string;
    updated_at?: Date;
}

interface EnvoyAwardCreationAttributes extends Optional<EnvoyAwardAttributes, 'id'> { }

class EnvoyAward
    extends Model<EnvoyAwardAttributes, EnvoyAwardCreationAttributes>
    implements EnvoyAwardAttributes {
    public id!: number;
    public envoy_id!: number;
    public award_id!: number;
    public reason!: string | null;

    public readonly created_at!: string;
    public readonly updated_at!: Date;
}

EnvoyAward.init(
    {
        id: {
            type: DataTypes.INTEGER,
            autoIncrement: true,
            primaryKey: true,
        },
        envoy_id: {
            type: DataTypes.BIGINT.UNSIGNED,
            allowNull: false,
            references: {
                model: User,
                key: 'id',
            },
        },
        award_id: {
            type: DataTypes.INTEGER,
            allowNull: false,
            references: {
                model: Award,
                key: 'id',
            },
        },
        reason: {
            type: DataTypes.TEXT,
            allowNull: true,
        },
        created_at: {
            type: DataTypes.DATEONLY,
            allowNull: false,
            defaultValue: DataTypes.NOW,
        },
    },
    {
        sequelize,
        tableName: 'envoy_awards',
        underscored: true,
        timestamps: true,
        updatedAt: 'updated_at',
        createdAt: 'created_at',
    },
);

// Associations
EnvoyAward.belongsTo(User, { foreignKey: 'envoy_id', as: 'envoy' });
EnvoyAward.belongsTo(Award, { foreignKey: 'award_id', as: 'award' });

export default EnvoyAward;
