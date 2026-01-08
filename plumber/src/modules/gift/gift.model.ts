import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';

class Gift extends Model {
  public id!: number;
  public name!: string;
  public image!: string;
  public points_required!: number;
  public createdAt!: Date;
  public updatedAt!: Date;
}

Gift.init(
  {
    id: {
      type: DataTypes.INTEGER,
      autoIncrement: true,
      primaryKey: true,
    },
    name: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    points_required: {
      type: DataTypes.INTEGER,
      allowNull: true,
    },
    image: {
      type: DataTypes.STRING,
      allowNull: true,
    },
  },
  {
    sequelize,
    tableName: 'plumber_gifts',
    timestamps: true,
  },
);

export default Gift;
