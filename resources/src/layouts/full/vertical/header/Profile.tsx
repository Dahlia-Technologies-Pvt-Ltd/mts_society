import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';
import {
  Box,
  Menu,
  Avatar,
  Typography,
  Divider,
  Button,
  IconButton,
  Stack
} from '@mui/material';
import * as dropdownData from './data';
import { IconMail } from '@tabler/icons';
import ProfileImg from '@src/assets/images/profile/user-1.jpg';
import { useSelector, useDispatch } from '@src/store/Store';
import { AppState } from '@src/store/Store';
import { addProfilePicture } from '@src/store/apps/eCommerce/ECommerceSlice';

const Profile = () => {
  const [anchorEl2, setAnchorEl2] = useState(null);
  const handleClick2 = (event: any) => {
    setAnchorEl2(event.currentTarget);
  };
  const handleClose2 = () => {
    setAnchorEl2(null);
  };
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const profilePicture = useSelector((state: AppState) => state.ecommerceReducer.profilePicture);
  if(profilePicture == null){
    const profile = localStorage.getItem('profilePicture');
    dispatch(addProfilePicture(profile));
  }
  const handleLogout = async () => {
     try {
        const appUrl = import.meta.env.VITE_API_URL;
        const API_URL = appUrl + '/api/logout';
        //const response = await axios.post(`${API_URL}`);
         // Clear session storage
         localStorage.removeItem('authToken');
         localStorage.removeItem('userCode');
         localStorage.removeItem('userId');
         localStorage.removeItem('userName');
         localStorage.removeItem('userEmail');
         localStorage.removeItem('userRole');
         localStorage.removeItem('userType');
         localStorage.removeItem('prevPage');
         localStorage.removeItem('societyToken');
         localStorage.removeItem('societyArray');
        window.location.reload();
        //navigate('/login');
     } catch (error) {
       console.error('Logout failed:', error);
     }
  };

  const userName=localStorage.getItem('userName');
  const userEmail=localStorage.getItem('userEmail');
  const userCode=localStorage.getItem('userCode');
  return (
    <Box>
      <IconButton
        size="large"
        aria-label="show 11 new notifications"
        color="inherit"
        aria-controls="msgs-menu"
        aria-haspopup="true"
        sx={{
          ...(typeof anchorEl2 === 'object' && {
            color: 'primary.main',
          }),
        }}
        onClick={handleClick2}
      >
        <Avatar
          src={profilePicture ? profilePicture : ''}
          alt={userName}
          sx={{
            width: 35,
            height: 35,
            border: 1,
            borderColor: 'black',
          }}
        />
      </IconButton>
      {/* ------------------------------------------- */}
      {/* Message Dropdown */}
      {/* ------------------------------------------- */}
      <Menu
        id="msgs-menu"
        anchorEl={anchorEl2}
        keepMounted
        open={Boolean(anchorEl2)}
        onClose={handleClose2}
        anchorOrigin={{ horizontal: 'right', vertical: 'bottom' }}
        transformOrigin={{ horizontal: 'right', vertical: 'top' }}
        sx={{
          '& .MuiMenu-paper': {
            width: '360px',
            p: 4,
          },
        }}
      >
        <Typography variant="h5">User Profile</Typography>
        <Stack direction="row" py={3} spacing={2} alignItems="center">
          <Avatar src={profilePicture ? profilePicture : ''} alt={userName} sx={{ width: 75, height: 75, border: 1, borderColor: 'black' }} />
          <Box>
            <Typography variant="subtitle2" color="textPrimary" fontWeight={600}>
              {userName}
            </Typography>
            <Typography variant="subtitle2" color="textSecondary">
            {userCode}
            </Typography>
            <Typography
              variant="subtitle2"
              color="textSecondary"
              display="flex"
              alignItems="center"
              gap={1}
            >
              <IconMail width={15} height={15} />
              {userEmail}
            </Typography>
          </Box>
        </Stack>
        <Divider />
        {dropdownData.profile.map((profile) => (
          <Box key={profile.title}>
            <Box sx={{ py: 2, px: 0 }} className="hover-text-primary">
              <Link to={profile.href}>
                <Stack direction="row" spacing={2}>
                  <Box
                    width="45px"
                    height="45px"
                    bgcolor="primary.light"
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                  >
                    <Avatar
                      src={profile.icon}
                      alt={profile.icon}
                      sx={{
                        width: 24,
                        height: 24,
                        borderRadius: 0,
                      }}
                    />
                  </Box>
                  <Box>
                    <Typography
                      variant="subtitle2"
                      fontWeight={600}
                      color="textPrimary"
                      className="text-hover"
                      noWrap
                      sx={{
                        width: '240px',
                        marginTop: '10px'
                      }}
                    >
                      {profile.title}
                    </Typography>
                    <Typography
                      color="textSecondary"
                      variant="subtitle2"
                      sx={{
                        width: '240px',
                      }}
                      noWrap
                    >
                      {profile.subtitle}
                    </Typography>
                  </Box>
                </Stack>
              </Link>
            </Box>
          </Box>
        ))}
        <Box mt={2}>
          <Button
            onClick={handleLogout}
            variant="outlined"
            color="primary"
            fullWidth
          >
            Logout
          </Button>
        </Box>
      </Menu>
    </Box>
  );
};

export default Profile;