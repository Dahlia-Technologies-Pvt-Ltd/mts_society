import React, { lazy } from 'react';
import { Navigate } from 'react-router-dom';
import Loadable from '../layouts/full/shared/loadable/Loadable';

/* ***Layouts**** */
const FullLayout = Loadable(lazy(() => import('../layouts/full/FullLayout')));
const BlankLayout = Loadable(lazy(() => import('../layouts/blank/BlankLayout')));

/* ****Admin Pages***** */
const AdminDashboard = Loadable(lazy(() => import('../admin/dashboard/AdminDashboard')));
const ChangePassword = Loadable(lazy(() => import('../admin/change-password/ChangePassword')));

/* ****Super Admin Pages***** */
const SuperAdminDashboard = Loadable(lazy(() => import('../superadmin/dashboard/SuperAdminDashboard')));
const SubscriptionList = Loadable(lazy(() => import('../superadmin/mastersubscription/SubscriptionList')));
const AddSubscription = Loadable(lazy(() => import('../superadmin/mastersubscription/AddSubscription')));

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
      { path: '/super-admin/dashboard', exact: true, element: <SuperAdminDashboard /> },
      { path: '/super-admin/subscription-plan-list', exact: true, element: <SubscriptionList /> },
      { path: '/super-admin/add-subscription', exact: true, element: <AddSubscription /> },
    ],
  },
];

export default Router;
