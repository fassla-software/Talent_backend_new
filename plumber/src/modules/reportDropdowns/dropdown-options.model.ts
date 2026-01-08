import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';

class ReportDropdownOption extends Model {
  public id!: number;
  public dropdown_type!: string;
  public key!: string;
  public value_en!: string;
  public value_ar!: string;
  public is_static!: boolean;
}

ReportDropdownOption.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
      allowNull: false,
    },
    dropdown_type: {
      type: DataTypes.STRING(50),
      allowNull: false,
    },
    key: {
      type: DataTypes.STRING(100),
      allowNull: false,
    },
    value_en: {
      type: DataTypes.STRING(255),
      allowNull: false,
    },
    value_ar: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    is_static: {
      type: DataTypes.BOOLEAN,
      defaultValue: false,
    },
    createdAt: {
      type: DataTypes.DATE,
      field: 'created_at',
    },
    updatedAt: {
      type: DataTypes.DATE,
      field: 'updated_at',
    },
  },
  {
    tableName: 'report_dropdown_options',
    timestamps: true,
    sequelize,
  },
);

export default ReportDropdownOption;