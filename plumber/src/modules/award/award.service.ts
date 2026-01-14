import { Op } from 'sequelize';
import Award from './award.model';
import EnvoyAward from './envoy_award.model';
import User from '../user/user.model';
import Role, { Roles } from '../role/role.model';
import ModelHasRoles from '../role/model_has_roles.model';

class AwardService {
    // Award CRUD
    async createAward(data: any) {
        return await Award.create(data);
    }

    async getAwards() {
        return await Award.findAll();
    }

    async getAwardById(id: number) {
        return await Award.findByPk(id);
    }

    async updateAward(id: number, data: any) {
        const award = await Award.findByPk(id);
        if (!award) throw new Error('Award not found');
        return await award.update(data);
    }

    async deleteAward(id: number) {
        const award = await Award.findByPk(id);
        if (!award) throw new Error('Award not found');
        return await award.destroy();
    }

    // Envoy Award CRUD
    async assignAwardToEnvoy(data: any) {
        const envoy = await User.findByPk(data.envoy_id);
        if (!envoy) throw new Error('Envoy not found');

        const award = await Award.findByPk(data.award_id);
        if (!award) throw new Error('Award not found');

        return await EnvoyAward.create(data);
    }

    async getEnvoyAwards(params: { page?: number; limit?: number; search?: string }) {
        const { page = 1, limit = 10, search } = params;
        const offset = (page - 1) * limit;


        const { count, rows } = await EnvoyAward.findAndCountAll({
            include: [
                {
                    model: User,
                    as: 'envoy',
                    attributes: ['id', 'name', 'phone'],
                    where: search ? {
                        [Op.or]: [
                            { name: { [Op.like]: `%${search}%` } },
                            { phone: { [Op.like]: `%${search}%` } },
                        ]
                    } : undefined,
                },
                { model: Award, as: 'award' },
            ],
            distinct: true,
            limit: Number(limit),
            offset: Number(offset),
            order: [['created_at', 'DESC']],
        });

        return {
            data: rows,
            total: count,
            page: Number(page),
            limit: Number(limit),
            totalPages: Math.ceil(count / limit),
        };
    }

    async getEnvoyAwardById(id: number) {
        return await EnvoyAward.findByPk(id, {
            include: [
                { model: User, as: 'envoy', attributes: ['id', 'name', 'phone'] },
                { model: Award, as: 'award' },
            ],
        });
    }

    async updateEnvoyAward(id: number, data: any) {
        const envoyAward = await EnvoyAward.findByPk(id);
        if (!envoyAward) throw new Error('Envoy Award not found');

        if (data.envoy_id) {
            const envoy = await User.findByPk(data.envoy_id);
            if (!envoy) throw new Error('Envoy not found');
        }

        if (data.award_id) {
            const award = await Award.findByPk(data.award_id);
            if (!award) throw new Error('Award not found');
        }

        return await envoyAward.update(data);
    }

    async deleteEnvoyAward(id: number) {
        const envoyAward = await EnvoyAward.findByPk(id);
        if (!envoyAward) throw new Error('Envoy Award not found');
        return await envoyAward.destroy();
    }

    async getAwardsByEnvoyId(envoyId: number) {
        return await EnvoyAward.findAll({
            where: { envoy_id: envoyId },
            include: [{ model: Award, as: 'award' }],
        });
    }

    // Get Envoys
    async getEnvoys() {
        const envoyRole = await Role.findOne({ where: { name: Roles.Envoy } });
        if (!envoyRole) return [];

        const userRoles = await ModelHasRoles.findAll({
            where: { role_id: envoyRole.id, model_type: 'App\\Models\\User' },
        });

        const userIds = userRoles.map((ur) => ur.model_id);

        return await User.findAll({
            where: { id: userIds },
            attributes: ['id', 'name', 'phone'],
        });
    }
}

export default new AwardService();
