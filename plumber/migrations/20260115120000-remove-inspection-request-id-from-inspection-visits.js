'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Get all foreign key constraints for the table
    const [constraints] = await queryInterface.sequelize.query(
      `SELECT CONSTRAINT_NAME 
       FROM information_schema.KEY_COLUMN_USAGE 
       WHERE TABLE_SCHEMA = DATABASE() 
       AND TABLE_NAME = 'inspection_visits' 
       AND COLUMN_NAME = 'inspection_request_id' 
       AND REFERENCED_TABLE_NAME IS NOT NULL`
    );

    // Remove all foreign key constraints related to inspection_request_id
    for (const constraint of constraints) {
      try {
        await queryInterface.sequelize.query(
          `ALTER TABLE inspection_visits DROP FOREIGN KEY \`${constraint.CONSTRAINT_NAME}\``
        );
      } catch (error) {
        // Constraint may not exist or already removed
        console.log(`Constraint ${constraint.CONSTRAINT_NAME} may not exist, skipping...`);
      }
    }

    // Remove the index if it exists
    try {
      const tableDescription = await queryInterface.describeTable('inspection_visits');
      if (tableDescription.inspection_request_id) {
        // Try to get index name
        const [indexes] = await queryInterface.sequelize.query(
          `SHOW INDEX FROM inspection_visits WHERE Column_name = 'inspection_request_id'`
        );
        
        for (const index of indexes) {
          if (index.Key_name !== 'PRIMARY') {
            await queryInterface.sequelize.query(
              `ALTER TABLE inspection_visits DROP INDEX \`${index.Key_name}\``
            );
          }
        }
      }
    } catch (error) {
      // Index may not exist
      console.log('Index removal skipped...');
    }
    
    // Remove the column
    const tableDescription = await queryInterface.describeTable('inspection_visits');
    if (tableDescription.inspection_request_id) {
      await queryInterface.removeColumn('inspection_visits', 'inspection_request_id');
    }
  },

  async down(queryInterface, Sequelize) {
    // Add the column back
    await queryInterface.addColumn('inspection_visits', 'inspection_request_id', {
      type: Sequelize.INTEGER,
      allowNull: true, // Set to true initially to allow existing records
      references: {
        model: 'inspection_requests',
        key: 'id',
      },
      onDelete: 'CASCADE',
      onUpdate: 'CASCADE',
    });
    
    // Add the index back
    await queryInterface.addIndex('inspection_visits', ['inspection_request_id']);
  },
};
