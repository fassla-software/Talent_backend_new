import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';

class Certificate extends Model {
  public id!: number;
  public certificate_id!: string;
  public plumber_id!: number | null;
  public user_phone!: string;
  public nationality_id!: string;
  public file_name!: string;
  public createdAt!: Date;
  public updatedAt!: Date;
}

Certificate.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    certificate_id: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    plumber_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true,
      references: {
        model: 'users',
        key: 'id',
      },
      onUpdate: 'SET NULL',
      onDelete: 'SET NULL',
    },
    user_phone: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    nationality_id: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    file_name: {
      type: DataTypes.STRING,
      allowNull: false,
    },
  },
  {
    sequelize,
    tableName: 'certificate',
    timestamps: true,
    underscored: true,
  },
);

// Associations
Certificate.belongsTo(User, {
  foreignKey: 'plumber_id', // This should match the column defined in your migration
  as: 'plumber', // Alias for eager loading
});
export default Certificate;
