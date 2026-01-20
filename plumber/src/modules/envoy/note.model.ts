import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';

class EnvoyNote extends Model {
    public id!: number;
    public envoy_id!: number;
    public client_id!: number;
    public content!: string;
    public readonly createdAt!: Date;
    public readonly updatedAt!: Date;
}

EnvoyNote.init(
    {
        id: {
            type: DataTypes.BIGINT.UNSIGNED,
            primaryKey: true,
            autoIncrement: true,
        },
        envoy_id: {
            type: DataTypes.BIGINT.UNSIGNED,
            allowNull: false,
            references: {
                model: User,
                key: 'id',
            },
            onDelete: 'CASCADE',
        },
        client_id: {
            type: DataTypes.BIGINT.UNSIGNED,
            allowNull: false,
            references: {
                model: User,
                key: 'id',
            },
            onDelete: 'CASCADE',
        },
        content: {
            type: DataTypes.TEXT,
            allowNull: false,
        },
    },
    {
        sequelize,
        tableName: 'envoy_notes',
        timestamps: true,
        underscored: true,
    }
);

// Associations
EnvoyNote.belongsTo(User, { foreignKey: 'envoy_id', as: 'envoy' });
EnvoyNote.belongsTo(User, { foreignKey: 'client_id', as: 'client' });
User.hasMany(EnvoyNote, { foreignKey: 'envoy_id', as: 'envoyNotes' });
User.hasMany(EnvoyNote, { foreignKey: 'client_id', as: 'clientNotes' });

export default EnvoyNote;
