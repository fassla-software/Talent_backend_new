import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db'; // Adjust the import path based on your project structure

interface ContactUsAttributes {
  id: bigint;
  name?: string;
  phone?: string;
  email?: string;
  message?: string;
  whatsapp?: string;
  messenger?: string;
  created_at?: Date;
  updated_at?: Date;
}

interface ContactUsCreationAttributes extends Optional<ContactUsAttributes, 'id'> {}

class ContactUs extends Model<ContactUsAttributes, ContactUsCreationAttributes> implements ContactUsAttributes {
  public id!: bigint;
  public name?: string;
  public phone?: string;
  public email?: string;
  public message?: string;
  public whatsapp?: string;
  public messenger?: string;
  public created_at?: Date;
  public updated_at?: Date;
}

ContactUs.init(
  {
    id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    message: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    phone: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    email: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    whatsapp: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    messenger: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: null,
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: null,
    },
  },
  {
    sequelize,
    tableName: 'contact_us',
    timestamps: true,
    createdAt: 'created_at',
    updatedAt: 'updated_at',
  },
);

export default ContactUs;
