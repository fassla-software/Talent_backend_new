import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db';

interface SegmentAttributes {
  id: number;
  description: string;
  minPoints: number;
  maxPoints?: number | null;
  pointsValue: number;
  createdAt?: Date | null;
  updatedAt?: Date | null;
}

interface SegmentCreationAttributes extends Optional<SegmentAttributes, 'id'> {}

class Segment extends Model<SegmentAttributes, SegmentCreationAttributes> implements SegmentAttributes {
  public id!: number;
  public description!: string;
  public minPoints!: number;
  public maxPoints?: number | null;
  public pointsValue!: number;
  public createdAt?: Date | null;
  public updatedAt?: Date | null;
}

Segment.init(
  {
    id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      primaryKey: true,
      autoIncrement: true,
    },
    description: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    minPoints: {
      type: DataTypes.INTEGER,
      allowNull: false,
    },
    maxPoints: {
      type: DataTypes.INTEGER,
      allowNull: true,
    },
    pointsValue: {
      type: DataTypes.DECIMAL(10, 2),
      allowNull: false,
      defaultValue: 0,
    },
  },
  {
    sequelize,
    tableName: 'plumber_segments',
  },
);

export default Segment;
