import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';

class Media extends Model {
  public id!: number;
  public name!: string;
  public path!: string;
  public createdAt!: Date;
  public updatedAt!: Date;
}

Media.init(
  {
    id: {
      type: DataTypes.BIGINT.UNSIGNED,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    path: {
      type: DataTypes.STRING,
      allowNull: false,
    },
  },
  {
    sequelize,
    tableName: 'media',
    timestamps: true,
  },
);

export default Media;
