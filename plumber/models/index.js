'use strict';

import { readdirSync } from 'fs';
import { basename as _basename, join } from 'path';
import Sequelize, { DataTypes } from 'sequelize';
import { config as _config } from 'dotenv';

// Load environment variables based on NODE_ENV (either .env.production or .env.development)
const env = process.env.NODE_ENV || 'development';
// Dynamically load the appropriate environment file
if (env === 'production') {
  _config({ path: '.env.production' });
} else {
  _config({ path: '.env.development' });
}

// const basename = _basename(__filename);
const db = {};

// Sequelize config based on environment variables
const config = {
  development: {
    username: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    host: process.env.DB_HOST,
    port: process.env.DB_PORT || 3306,
    dialect: process.env.DB_CONNECTION || 'mysql',
  },
  test: {
    username: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    host: process.env.DB_HOST,
    port: process.env.DB_PORT || 3306,
    dialect: process.env.DB_CONNECTION || 'mysql',
  },
  production: {
    username: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    host: process.env.DB_HOST,
    port: process.env.DB_PORT || 3306,
    dialect: process.env.DB_CONNECTION || 'mysql',
  },
};

// Initialize Sequelize with the corresponding environment config
let sequelize;
if (config[env].use_env_variable) {
  sequelize = new Sequelize(process.env[config[env].use_env_variable], config[env]);
} else {
  sequelize = new Sequelize(config[env].database, config[env].username, config[env].password, config[env]);
}

// Read all model files and import them into the db object
readdirSync(__dirname)
  .filter(file => {
    return file.indexOf('.') !== 0 && file !== _basename && file.slice(-3) === '.js' && file.indexOf('.test.js') === -1;
  })
  .forEach(file => {
    // eslint-disable-next-line @typescript-eslint/no-var-requires
    const model = require(join(__dirname, file))(sequelize, DataTypes);
    db[model.name] = model;
  });

// Establish model associations
Object.keys(db).forEach(modelName => {
  if (db[modelName].associate) {
    db[modelName].associate(db);
  }
});

db.sequelize = sequelize;
db.Sequelize = Sequelize;

export default db;
