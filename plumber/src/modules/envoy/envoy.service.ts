import EnvoySetting from './envoy.model';
import EnvoyNote from './note.model';
import Ticket from '../ticket/ticket.model';
import User from '../user/user.model';
import Plumber from '../plumber/plumber.model';
import Trader from '../trader/trader.model';
import HttpError from '../../utils/HttpError';
import bcrypt from 'bcrypt';
import jwt from 'jsonwebtoken';
import { Roles } from '../role/role.model';
import { assignRole, getRole } from '../role/role.service';
import { generateUniqueReferralCode } from '../../utils/generateReferCode';
import { PlumberAccountStatus } from '../plumber/plumber.model';
import { TraderActivityStatus } from '../trader/trader.model';
import RegistrationBonusRule from '../user/registration_bonus.model';
import { PendingBonus } from '../user/pending-bonus.model';
import { Op } from 'sequelize';
import SMSSender from '../../utils/smsSender';
import { logStatusChange } from '../statusHistory/status-history.service';
import { ClientType } from '../statusHistory/status-history.model';
import NotificationUnique from '../user/notification.model';
import InspectionVisit from '../inspectionVisit/inspection-visit.model';
import VisitReport from '../inspectionVisit/visit-report.model';
import InspectionRequest from '../inspectionRequest/inspection_request.model';
import Media from '../media/media.model';
import sequelize from '../../config/db';

export const getEnvoySettingByUserId = async (userId: number) => {
    return await EnvoySetting.findOne({ where: { user_id: userId } });
};

interface IRegisterUserByEnvoy {
    name: string;
    password: string;
    phone: string;
    city: string;
    area: string;
    referralCode?: string;
    nationality_id?: string;
    nationality_image1?: string;
    nationality_image2?: string;
}

export const registerUserByEnvoy = async (
    inspectorId: number,
    newUser: IRegisterUserByEnvoy,
    role: Roles
) => {
    const { name, password, phone, city, area, referralCode, nationality_id, nationality_image1, nationality_image2 } = newUser;

    // Validate role
    if (role !== Roles.PLUMBER && role !== Roles.TRADER) {
        throw new HttpError('Role must be either plumber or trader', 400);
    }

    // Check if phone already exists
    const existingUser = await User.findOne({ where: { phone } });
    if (existingUser) {
        throw new HttpError('Phone number already registered', 400);
    }

    let referrer: User | null = null;

    if (referralCode) {
        referrer = await User.findOne({ where: { refer_code: referralCode } });
        if (!referrer) {
            throw new HttpError('Referral code is invalid', 400);
        }
    }

    const hashedPassword = await bcrypt.hash(password, 10);
    const userReferCode = await generateUniqueReferralCode();

    // Create user with phone verified and active
    const user = await User.create({
        name,
        password: hashedPassword,
        phone,
        is_active: true, // Envoy-registered users are active immediately
        phone_verified_at: new Date(), // Mark phone as verified
        refer_code: userReferCode,
    });

    if (!user) throw new HttpError('User not created', 404);

    let plumber = null;
    let trader = null;

    if (role === Roles.PLUMBER) {
        plumber = await Plumber.create({
            user_id: user.id,
            city,
            area,
            inspector_id: inspectorId,
            status: PlumberAccountStatus.PENDING,
            is_verified: true,
            nationality_id: nationality_id || undefined,
            nationality_image1: nationality_image1 || undefined,
            nationality_image2: nationality_image2 || undefined,
        });

        // Log initial status
        await logStatusChange(
            plumber.id,
            ClientType.PLUMBER,
            null,
            PlumberAccountStatus.PENDING
        );
    } else if (role === Roles.TRADER) {
        trader = await Trader.create({
            user_id: user.id,
            city,
            area,
            inspector_id: inspectorId,
            status: TraderActivityStatus.PENDING,
            is_verified: true,
            nationality_id: nationality_id || undefined,
            nationality_image1: nationality_image1 || undefined,
            nationality_image2: nationality_image2 || undefined,
        });

        // Log initial status
        await logStatusChange(
            trader.id,
            ClientType.TRADER,
            null,
            TraderActivityStatus.PENDING
        );
    }

    const assignedRole = await assignRole(user.id, role);

    // Handle referral bonuses
    const bonusRule = await RegistrationBonusRule.findOne({
        where: {
            start_date: { [Op.lte]: new Date() },
            end_date: { [Op.gte]: new Date() },
        },
        order: [['created_at', 'DESC']],
    });

    if (bonusRule) {
        await PendingBonus.create({
            new_user_id: user.id,
            referrer_id: referrer ? referrer.id : null,
            points: bonusRule.points,
            point_type: bonusRule.point_type,
        });

        console.log(
            `Pending bonus (${bonusRule.points}) saved for new user ${user.id} with referrer ${referrer?.id || 'none'}`
        );
    }

    // Generate token for the new user
    const payload = { id: user.id, role };
    const token = jwt.sign(payload, process.env.KEY!);

    try {
        const smsSender = new SMSSender();
        const message = `Welcome to Talanet! You have been registered successfully. Your credentials: Phone: ${phone}, Password: ${password}.`;
        await smsSender.sendMessage(phone, message);
        smsSender
            .checkBalance()
            .then(balance => console.log('SMS Balance:', balance))
            .catch(error => console.error('Error Checking Balance:', error.message));
    } catch (error) {
        console.error('Failed to send welcome SMS:', error);
    }

    if (role === Roles.PLUMBER && plumber) {
        return { token, role: assignedRole, ...user.get(), ...plumber.get() };
    } else if (role === Roles.TRADER && trader) {
        return { token, role: assignedRole, ...user.get(), ...trader.get() };
    } else {
        return { token, role: assignedRole, ...user.get() };
    }
};

export const getNotifications = async (userId: number) => {
    return await NotificationUnique.findAll({
        where: { user_id: userId },
        order: [['created_at', 'DESC']],
    });
};

export const createNote = async (envoyId: number, clientId: number, content: string) => {
    return await EnvoyNote.create({
        envoy_id: envoyId,
        client_id: clientId,
        content,
    });
};

export const getEnvoyClients = async (envoyId: number, name?: string, phone?: string) => {
    const userWhere: any = {
        status: 'APPROVED',
    };

    const searchConditions: any = {};
    if (name) searchConditions.name = { [Op.like]: `%${name}%` };
    if (phone) searchConditions.phone = { [Op.like]: `%${phone}%` };

    Object.assign(userWhere, searchConditions);

    const hasExplicitSearch = Object.keys(searchConditions).length > 0;

    // Common include for both Traders and Plumbers
    const getInclude = () => [
        {
            model: User,
            as: 'user',
            where: userWhere,
            include: [
                {
                    model: Ticket,
                    as: 'client_tickets',
                },
                {
                    model: EnvoyNote,
                    as: 'clientNotes',
                },
                {
                    model: InspectionRequest,
                    as: 'requests',
                },
            ],
        },
        {
            model: User,
            as: 'inspector',
            attributes: ['id', 'name'],
        },
        {
            model: InspectionVisit,
            as: 'inspectionVisits',
            include: [
                {
                    model: VisitReport,
                    as: 'visitReport',
                }
            ],
            where: { inspector_id: envoyId },
            required: false, // LEFT JOIN to allow checking for existence via top-level WHERE
        },
    ];

    const mainFilter: any = {};
    if (!hasExplicitSearch) {
        mainFilter[Op.or] = [
            { inspector_id: envoyId },
            { '$inspectionVisits.id$': { [Op.ne]: null } }
        ];
    }

    const traders = await Trader.findAll({
        where: mainFilter,
        include: getInclude(),
        subQuery: false, // Required for filtering on included associations with OR
    });

    const plumbers = await Plumber.findAll({
        where: mainFilter,
        include: getInclude(),
        subQuery: false, // Required for filtering on included associations with OR
    });

    const processClient = (client: any, role: string) => {
        const json = client.toJSON();
        const clientVisits = json.inspectionVisits || [];
        const clientTasks = json.user?.requests || [];

        const result = {
            ...json,
            role,
            inspector_name: json.inspector?.name || null,
            total_count: clientVisits.length,
            last_visit_date: clientVisits.length > 0
                ? new Date(Math.max(...clientVisits.map((v: any) => new Date(v.check_in_at || v.createdAt).getTime())))
                : null,
            total_sales_values: clientVisits.reduce((sum: number, visit: any) =>
                sum + Number(visit.visitReport?.sales_value || 0), 0),
        };

        if (result.user) {
            result.user.client_visits = clientVisits;
            result.user.client_tasks = clientTasks;
            result.user.client_notes = result.user.clientNotes || [];
            delete (result.user as any).clientNotes;
            delete (result.user as any).requests;
        }

        // Remove redundant inspectionVisits
        delete (result as any).inspectionVisits;

        return result;
    };

    const clients = [
        ...traders.map(t => processClient(t, 'trader')),
        ...plumbers.map(p => processClient(p, 'plumber')),
    ];

    return clients;
};

export const updateProfile = async (
    userId: number,
    data: { name?: string; phone?: string; region?: string; profile_photo?: string }
) => {
    const transaction = await sequelize.transaction();
    try {
        const user = await User.findByPk(userId, { transaction });
        if (!user) throw new HttpError('User not found', 404);

        const envoySetting = await EnvoySetting.findOne({ where: { user_id: userId }, transaction });
        if (!envoySetting) throw new HttpError('Envoy settings not found', 404);

        // Update User
        if (data.name) user.name = data.name;
        if (data.phone) {
            const existingUser = await User.findOne({
                where: { phone: data.phone, id: { [Op.ne]: userId } },
                transaction
            });
            if (existingUser) throw new HttpError('Phone number already in use', 400);
            user.phone = data.phone;
        }

        // Update Profile Photo
        if (data.profile_photo) {
            const extension = data.profile_photo.split('.').pop();
            let media = await Media.findByPk(user.mediaId || 0, { transaction });
            if (media) {
                media.name = data.profile_photo;
                media.src = data.profile_photo;
                media.extention = extension || 'jpg';
                await media.save({ transaction });
            } else {
                media = await Media.create({
                    name: data.profile_photo,
                    src: data.profile_photo,
                    type: 'image',
                    extention: extension || 'jpg',
                }, { transaction });
                user.mediaId = media.id;
            }
        }

        await user.save({ transaction });

        // Update Envoy Setting
        if (data.region !== undefined) {
            envoySetting.region = data.region;
            await envoySetting.save({ transaction });
        }

        const updatedUser = await User.findByPk(userId, {
            include: [
                { model: Media, as: 'media', attributes: ['src', 'extention'] },
                { model: EnvoySetting, as: 'envoySetting' }
            ],
            transaction // Include this in the same transaction
        });

        if (!updatedUser) throw new HttpError('User not found after update', 404);

        const role = await getRole(userId);

        await transaction.commit();

        return {
            user: {
                ...updatedUser.toJSON(),
                role,
                envoySetting: updatedUser.envoySetting
            },
        };
    } catch (error) {
        await transaction.rollback();
        throw error;
    }
};