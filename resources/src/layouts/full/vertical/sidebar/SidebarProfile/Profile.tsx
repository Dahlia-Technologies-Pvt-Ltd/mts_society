import { Box, Avatar, Typography, IconButton, Tooltip, useMediaQuery } from '@mui/material';
import { useSelector } from '@src/store/Store';
import img1 from '@src/assets/images/profile/user-1.jpg';
import { IconPower } from '@tabler/icons';
import { AppState } from '@src/store/Store';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';

export const Profile = () => {
  const customizer = useSelector((state: AppState) => state.customizer);
  const lgUp = useMediaQuery((theme: any) => theme.breakpoints.up('lg'));
  const hideMenu = lgUp ? customizer.isCollapse && !customizer.isSidebarHover : '';
  const navigate = useNavigate();
  const handleLogout = async () => {
    try {
       const appUrl = import.meta.env.VITE_API_URL;
       const API_URL = appUrl + '/api/logout'; // API service URL
       //const response = await axios.post(`${API_URL}`);
        // Clear session storage
       sessionStorage.removeItem('authToken');
       sessionStorage.removeItem('userCode');
       sessionStorage.removeItem('userId');
       sessionStorage.removeItem('userName');
       sessionStorage.removeItem('userEmail');
       sessionStorage.removeItem('userRole');
       sessionStorage.removeItem('userType');
       window.location.reload();
       //navigate('/login');
    } catch (error) {
      console.error('Logout failed:', error);
    }
 };
  const userName=sessionStorage.getItem('userName');
  const userEmail=sessionStorage.getItem('userEmail');
  const userRole=sessionStorage.getItem('userRole');
  return (
    <>
      {/* {!hideMenu ? (
        <>
        <Box
      display={'flex'}
      alignItems="center"
      gap={1}
      sx={{ m: 3, p: 2, bgcolor: `${'secondary.light'}` }}
    >
          <Avatar alt="Remy Sharp" src={img1} />
          <Box>
            <Typography variant="h6">{userName} </Typography>
            <Typography variant="caption">{userRole}</Typography>
          </Box>
          <Box sx={{ ml: 'auto' }}>
            <Tooltip title="Logout" placement="top">
              <IconButton
                color="primary"
                aria-label="logout"
                size="small"
                onClick={handleLogout}
              >
                <IconPower size="20" />
              </IconButton>
            </Tooltip>
          </Box>
          </Box>
        </>
      ) : (
        ''
      )} */}
    </>
  );
};
