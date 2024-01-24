import { FC } from 'react';
import CssBaseline from "@mui/material/CssBaseline";
import { ThemeProvider } from '@mui/material';
import { useRoutes, useLocation, Navigate } from 'react-router-dom';
import { useSelector } from '@src/store/Store';
import { ThemeSettings } from './theme/Theme';
import RTL from './layouts/full/shared/customizer/RTL';
import ScrollToTop from './components/shared/ScrollToTop';
import Router from './routes/Router';
import { AppState } from './store/Store';

const App: FC = () => {
  const authToken = localStorage.getItem('authToken');
  const userType = localStorage.getItem('userType');
  const location = useLocation();

  // Array of paths to check against
  const allowedPaths = ['/login', '/forgot-password'];

  if (authToken) {
    // If the user is authenticated.
    if(userType == '0'){
      if(!location.pathname.startsWith('/user/')){
        return <Navigate to="/user/dashboard" />;
      }
    }else if(userType == '1'){
      if(!location.pathname.startsWith('/admin/')){
        return <Navigate to="/admin/dashboard" />;
      }
    }else if(userType == '2'){
      if(!location.pathname.startsWith('/super-admin/')){
        return <Navigate to="/super-admin/dashboard" />;
      }
    }

    if (location.pathname === '/login' || location.pathname === '/') {
      if(userType == '0'){
        return <Navigate to="/user/dashboard" />;
      }else if (userType == '1'){
        return <Navigate to="/admin/dashboard" />;
      }else if (userType == '2'){
        return <Navigate to="/super-admin/dashboard" />;
      }
    }
  } else {
    if (!allowedPaths.includes(location.pathname) && !location.pathname.startsWith('/reset-password/')) {
      return <Navigate to="/login" />;
    }
  }


  const routing = useRoutes(Router);
  const theme = ThemeSettings();
  const customizer = useSelector((state: AppState) => state.customizer);
  return (
    <ThemeProvider theme={theme}>
      <RTL direction={customizer.activeDir}>
        <CssBaseline />
        <ScrollToTop>{routing}</ScrollToTop>
      </RTL>
    </ThemeProvider>
  );
};

export default App;