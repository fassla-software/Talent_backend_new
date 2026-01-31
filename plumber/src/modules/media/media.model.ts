import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';

class Media extends Model {
  public id!: number;
  public name!: string;
  public src!: string;
  public type!: string;
  public extention!: string;
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
    src: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    type: {
      type: DataTypes.STRING,
      allowNull: true,
      defaultValue: 'image',
    },
    extention: {
      type: DataTypes.STRING,
      allowNull: true,
    },
  },
  {
    sequelize,
    tableName: 'media',
    timestamps: true,
    underscored: true,
  },
);

export default Media;
