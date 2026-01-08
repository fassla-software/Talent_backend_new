import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';

class AppConfig extends Model {
  public id!: number; // Primary Key
  public key!: string; // Configuration key
  public value!: string; // Configuration value
}

AppConfig.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
      allowNull: false,
    },
    key: {
      type: DataTypes.STRING,
      allowNull: false,
      unique: true, // Ensures no duplicate keys
    },
    value: {
      type: DataTypes.TEXT,
      allowNull: false,
    },
      createdAt: {
        type: DataTypes.DATE,
        field: 'created_at', // Maps to the database column
      },
      updatedAt: {
        type: DataTypes.DATE,
        field: 'updated_at', // Maps to the database column
      },
    },
    {
      tableName: 'app_configs',
      timestamps: true, // Enables Sequelize to use createdAt/updatedAt
    sequelize,
  
  },
);

export default AppConfig;
