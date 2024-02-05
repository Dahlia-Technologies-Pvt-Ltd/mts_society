import React, { lazy } from 'react';
import { Navigate } from 'react-router-dom';
import Loadable from '../layouts/full/shared/loadable/Loadable';

/* ***Layouts**** */
const FullLayout = Loadable(lazy(() => import('../layouts/full/FullLayout')));
const BlankLayout = Loadable(lazy(() => import('../layouts/blank/BlankLayout')));

/* ****Admin Pages***** */
const AdminDashboard = Loadable(lazy(() => import('../admin/dashboard/AdminDashboard')));
const ChangePassword = Loadable(lazy(() => import('../common/change-password/ChangePassword')));
const TowerList = Loadable(lazy(() => import('../admin/tower/TowerList')));
const AddTower = Loadable(lazy(() => import('../admin/tower/AddTower')));
const FloorList = Loadable(lazy(() => import('../admin/floor/FloorList')));
const AddFloor = Loadable(lazy(() => import('../admin/floor/AddFloor')));
const FlatList = Loadable(lazy(() => import('../admin/flat/FlatList')));
const AddFlat = Loadable(lazy(() => import('../admin/flat/AddFlat')));

/* ****Super Admin Pages***** */
const SuperAdminDashboard = Loadable(lazy(() => import('../superadmin/dashboard/SuperAdminDashboard')));
const SubscriptionList = Loadable(lazy(() => import('../superadmin/mastersubscription/SubscriptionList')));
const AddSubscription = Loadable(lazy(() => import('../superadmin/mastersubscription/AddSubscription')));
const AddMasterUser = Loadable(lazy(() => import('../superadmin/masteruser/AddMasterUser')));
const MasterUserList = Loadable(lazy(() => import('../superadmin/masteruser/MasterUserList')));
const AccountSetting = Loadable(lazy(() => import('../common/account-settings/AccountSettings')))
const SystemSettings = Loadable(lazy(() => import('../superadmin/system-settings/SystemSettings')));
const MasterSocietyList = Loadable(lazy(() => import('../superadmin/mastersociety/MasterSocietyList')));
const AddMasterSociety = Loadable(lazy(() => import('../superadmin/mastersociety/AddMasterSociety')));
const MasterSocietyDetails = Loadable(lazy(() => import('../superadmin/mastersociety/MasterSocietyDetails')));
const EmailTemplate = Loadable(lazy(() => import('../superadmin/email/EmailTemplate')));
const MasterUserDetails = Loadable(lazy(() => import('../superadmin/masteruser/MasterUserDetails')));

// authentication
const Login = Loadable(lazy(() => import('../auth/Login')));
const ForgotPassword = Loadable(lazy(() => import('../auth/ForgotPassword')));
const ResetPassword = Loadable(lazy(() => import('../auth/ResetPassword')));
const Register = Loadable(lazy(() => import('../auth/Register')));
const Error = Loadable(lazy(() => import('../auth/Error')));

const Router = [
  {
    path: '/',
    element: <BlankLayout />,
    children: [
      { path: '/', element: <Login /> },
      { path: '/login', element: <Login /> },
      { path: '/forgot-password', element: <ForgotPassword /> },
      { path: '/reset-password/:token', element: <ResetPassword /> },
      { path: '/register', element: <Register /> },
      { path: '/auth/404', element: <Error /> },
      { path: '*', element: <Navigate to="/auth/404" /> },
    ],
  },
  {
    path: '/',
    element: <FullLayout />,
    children: [
      { path: '/admin/dashboard', exact: true, element: <AdminDashboard /> },
      { path: '/admin/change-password', exact: true, element: <ChangePassword /> },
      { path: '/admin/account-setting', exact: true, element: <AccountSetting /> },
      { path: '/admin/tower-list', exact: true, element: <TowerList /> },
      { path: '/admin/add-tower', exact: true, element: <AddTower /> },
      { path: '/admin/edit-tower/:id', exact: true, element: <AddTower /> },
      { path: '/admin/floor-list', exact: true, element: <FloorList /> },
      { path: '/admin/add-floor', exact: true, element: <AddFloor /> },
      { path: '/admin/edit-floor/:id', exact: true, element: <AddFloor /> },
      { path: '/admin/flat-list', exact: true, element: <FlatList /> },
      { path: '/admin/add-flat', exact: true, element: <AddFlat /> },
      { path: '/admin/edit-flat/:id', exact: true, element: <AddFlat /> },



      /**/
      { path: '/super-admin/dashboard', exact: true, element: <SuperAdminDashboard /> },
      { path: '/super-admin/subscription-plan-list', exact: true, element: <SubscriptionList /> },
      { path: '/super-admin/add-subscription', exact: true, element: <AddSubscription /> },
      { path: '/super-admin/edit-subscription/:id', exact: true, element: <AddSubscription /> },
      { path: '/super-admin/society-admin-list', exact: true, element: <MasterUserList /> },
      { path: '/super-admin/add-society-admin', exact: true, element: <AddMasterUser /> },
      { path: '/super-admin/society-admin-details/:id', exact: true, element: <MasterUserDetails /> },
      { path: '/super-admin/edit-society-admin/:id', exact: true, element: <AddMasterUser /> },
      { path: '/super-admin/change-password', exact: true, element: <ChangePassword /> },
      { path: '/super-admin/account-setting', exact: true, element: <AccountSetting /> },
      { path: '/super-admin/system-setting', exact: true, element: <SystemSettings /> },
      { path: '/super-admin/society-list', exact: true, element: <MasterSocietyList /> },
      { path: '/super-admin/add-society', exact: true, element: <AddMasterSociety /> },
      { path: '/super-admin/edit-society/:id', exact: true, element: <AddMasterSociety /> },
      { path: '/super-admin/society-details/:id', exact: true, element: <MasterSocietyDetails /> },
      { path: '/super-admin/email-template', exact: true, element: <EmailTemplate /> },
      
    ],
  },
];

export default Router;
