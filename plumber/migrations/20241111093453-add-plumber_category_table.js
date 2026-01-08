'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Check if the table already exists
    const tables = await queryInterface.sequelize.getQueryInterface().showAllTables();

    if (!tables.includes('plumber_categories')) {
      await queryInterface.createTable('plumber_categories', {
        id: {
          type: Sequelize.INTEGER,
          primaryKey: true,
          autoIncrement: true,
        },
        name: {
          type: Sequelize.STRING,
          allowNull: false,
        },
        parent_id: {
          type: Sequelize.INTEGER,
          allowNull: true,
          references: {
            model: 'plumber_categories',
            key: 'id',
          },
          onDelete: 'CASCADE',
          onUpdate: 'CASCADE',
        },
        image: {
          type: Sequelize.STRING, // Add 'image' column if it's not already there
          allowNull: true,
        },
        created_at: {
          type: Sequelize.DATE,
          allowNull: false,
          defaultValue: Sequelize.NOW,
        },
        updated_at: {
          type: Sequelize.DATE,
          allowNull: false,
          defaultValue: Sequelize.NOW,
        },
      });
    } else {
      // Add the column only if it doesn't exist
      const columns = await queryInterface.describeTable('plumber_categories');
      if (!columns['image']) {
        await queryInterface.addColumn('plumber_categories', 'image', {
          type: Sequelize.STRING,
          allowNull: true,
        });
      }
    }
  },

  async down(queryInterface) {
    await queryInterface.dropTable('plumber_categories');
  },
};
