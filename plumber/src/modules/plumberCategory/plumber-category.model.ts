import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db';

interface PlumberCategoryAttributes {
  id: number;
  name: string;
  image: string;
  points: number;
  product_flag: boolean;
  parent_id?: number | null;
  createdAt: Date;
  updatedAt: Date;
  parent?: PlumberCategory;
  subcategories?: PlumberCategory[];
}

interface PlumberCategoryCreationAttributes
  extends Optional<PlumberCategoryAttributes, 'id' | 'createdAt' | 'updatedAt' | 'parent_id'> {}

class PlumberCategory extends Model<PlumberCategoryAttributes, PlumberCategoryCreationAttributes> {
  public id!: number;
  public name!: string;
  public image!: string;
  public parent_id?: number | null;
  public points!: number;
  public product_flag!: boolean;
  public createdAt!: Date;
  public updatedAt!: Date;

  // Association fields
  public parent?: PlumberCategory;
  public subcategories?: PlumberCategory[];
}

PlumberCategory.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    image: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    points: {
      type: DataTypes.INTEGER,
      defaultValue: 0,
    },
    product_flag: {
      type: DataTypes.BOOLEAN,
      defaultValue: true,
    },
    parent_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      references: {
        model: 'plumber_categories',
        key: 'id',
      },
      onDelete: 'CASCADE',
      onUpdate: 'CASCADE',
    },
    createdAt: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: DataTypes.NOW,
    },
    updatedAt: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: DataTypes.NOW,
    },
  },
  {
    sequelize,
    tableName: 'plumber_categories',
    timestamps: true,
    underscored: true,
  },
);

// Set up self-referencing associations
PlumberCategory.hasMany(PlumberCategory, { as: 'subcategories', foreignKey: 'parent_id' });
PlumberCategory.belongsTo(PlumberCategory, { as: 'parent', foreignKey: 'parent_id' });

export default PlumberCategory;
