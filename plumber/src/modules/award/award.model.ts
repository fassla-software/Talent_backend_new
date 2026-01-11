import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db';

interface AwardAttributes {
    id: number;
    title: string;
    description: string | null;
    created_at?: Date;
    updated_at?: Date;
}

interface AwardCreationAttributes extends Optional<AwardAttributes, 'id'> { }

class Award extends Model<AwardAttributes, AwardCreationAttributes> implements AwardAttributes {
    public id!: number;
    public title!: string;
    public description!: string | null;

    public readonly created_at!: Date;
    public readonly updated_at!: Date;
}

Award.init(
    {
        id: {
            type: DataTypes.INTEGER,
            autoIncrement: true,
            primaryKey: true,
        },
        title: {
            type: DataTypes.STRING,
            allowNull: false,
        },
        description: {
            type: DataTypes.TEXT,
            allowNull: true,
        },
    },
    {
        sequelize,
        tableName: 'awards',
        underscored: true,
        timestamps: true,
    },
);

export default Award;
