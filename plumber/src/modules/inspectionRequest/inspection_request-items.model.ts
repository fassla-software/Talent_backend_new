import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import PlumberCategory from '../plumberCategory/plumber-category.model';

class InspectionRequestItem extends Model {
  public id!: number;
  public inspection_request_id!: number;
  public subcategory_id!: number | null;
  public count!: number;
  public createdAt!: Date;
  public updatedAt!: Date;
  subcategory!: PlumberCategory;
  category_name?: string;
  category_count?: string;
  category_points?: string;
  topCategoryName?: string;
}

InspectionRequestItem.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    inspection_request_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      references: {
        model: 'inspection_requests',
        key: 'id',
      },
      onDelete: 'CASCADE',
      onUpdate: 'CASCADE',
    },
    subcategory_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      references: {
        model: 'plumber_categories',
        key: 'id',
      },
      onDelete: 'SET NULL',
      onUpdate: 'CASCADE',
    },
    count: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
    },
  },
  {
    sequelize,
    tableName: 'inspection_requests_items',
    timestamps: true,
    underscored: true,
  },
);

InspectionRequestItem.belongsTo(PlumberCategory, {
  foreignKey: 'subcategory_id',
  as: 'subcategory', // Alias for the association
});

PlumberCategory.belongsTo(PlumberCategory, {
  foreignKey: 'parent_id',
  as: 'parentCategory',
});

PlumberCategory.hasMany(PlumberCategory, {
  foreignKey: 'parent_id',
  as: 'children',
});

export default InspectionRequestItem;
