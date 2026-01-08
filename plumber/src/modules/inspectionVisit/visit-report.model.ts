import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';

class VisitReport extends Model {
  public id!: number;
  // Customer Information (معلومات العميل)
  public customer_name!: string;
  public company_name?: string | null;
  public location!: string;
  public region_province?: string | null;
  public phone!: string;
  public email?: string | null;
  public client_type?: string | null; // من report_dropdown_options
  public visit_type?: string | null; // من report_dropdown_options

  // Visit Details (تفاصيل الزيارة)
  public visit_result!: string; // من report_dropdown_options
  public interest_level?: string | null; // من report_dropdown_options
  public purchase_readiness?: string | null; // من report_dropdown_options
  public authority_level?: string | null; // من report_dropdown_options
  public sales_value?: number | null;
  public planned_purchase_date?: Date | null;
  public outcome_classification?: string | null; // من report_dropdown_options
  public next_action?: string | null; // من report_dropdown_options

  // Sales Classification (تصنيف المبيعات)
  public sales_classification?: string | null; // من report_dropdown_options (مباشر / غير مباشر)

  // Additional Notes
  public additional_notes?: string | null;

  // Photos and Documents
  public photos?: string[] | null;

  public createdAt!: Date;
  public updatedAt!: Date;
}

VisitReport.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    customer_name: {
      type: DataTypes.STRING(255),
      allowNull: false,
    },
    company_name: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    location: {
      type: DataTypes.STRING(500),
      allowNull: false,
    },
    region_province: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    phone: {
      type: DataTypes.STRING(20),
      allowNull: false,
    },
    email: {
      type: DataTypes.STRING(255),
      allowNull: true,
    },
    client_type: {
      type: DataTypes.STRING(100),
      allowNull: true,
    },
    visit_type: {
      type: DataTypes.STRING(100),
      allowNull: true,
    },
    visit_result: {
      type: DataTypes.STRING(100),
      allowNull: false,
    },
    interest_level: {
      type: DataTypes.STRING(100),
      allowNull: true,
    },
    purchase_readiness: {
      type: DataTypes.STRING(100),
      allowNull: true,
    },
    authority_level: {
      type: DataTypes.STRING(100),
      allowNull: true,
    },
    sales_value: {
      type: DataTypes.DECIMAL(15, 2),
      allowNull: true,
    },
    planned_purchase_date: {
      type: DataTypes.DATEONLY,
      allowNull: true,
    },
    outcome_classification: {
      type: DataTypes.STRING(100),
      allowNull: true,
    },
    next_action: {
      type: DataTypes.STRING(100),
      allowNull: true,
    },
    sales_classification: {
      type: DataTypes.STRING(100),
      allowNull: true,
    },
    additional_notes: {
      type: DataTypes.TEXT,
      allowNull: true,
    },
    photos: {
      type: DataTypes.JSON,
      allowNull: true,
      get() {
        const rawValue = this.getDataValue('photos');
        return rawValue ? JSON.parse(rawValue as string) : null;
      },
      set(value: string[] | null) {
        this.setDataValue('photos', value ? JSON.stringify(value) : null);
      },
    },
  },
  {
    sequelize,
    tableName: 'report_visits',
    timestamps: true,
    underscored: true,
  },
);

export default VisitReport;

