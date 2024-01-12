/* eslint-disable @typescript-eslint/no-unused-vars */
import React from 'react';

import { useSelector } from '@src/store/Store';
import {
  ListItemText,
  Box,
  Avatar,
  ListItemButton,
  Typography,
  Stack,
  ListItemAvatar,
  useTheme,
} from '@mui/material';

import { IconStar, IconTrash } from '@tabler/icons';

type Props = {
  onContactClick: (event: React.MouseEvent<HTMLElement>) => void;
  onStarredClick: React.MouseEventHandler<SVGElement>;
  onDeleteClick: React.MouseEventHandler<SVGElement>;
  id: string | number;
  firstname: string;
  lastname: string;
  image: string;
  department: string;
  starred: boolean;
  active: any;
};

const ContactListItem = ({
  onContactClick,
  onStarredClick,
  onDeleteClick,
  id,
  firstname,
  lastname,
  image,
  department,
  starred,
  active,
}: Props) => {
  const customizer = useSelector((state) => state.customizer);
  const br = `${customizer.borderRadius}px`;

  const theme = useTheme();

  const warningColor = theme.palette.warning.main;

  return (
    <ListItemButton sx={{ mb: 1 }} selected={active}>
      <ListItemText>
        <Stack direction="row" gap="10px" alignItems="center">
          <Box mr="auto" onClick={onContactClick}>
            <Typography variant="subtitle1" noWrap fontWeight={600} sx={{ maxWidth: '150px' }}>
              {firstname} {lastname}
            </Typography>
            <Typography variant="body2" color="text.secondary" noWrap>
              {department}
            </Typography>
          </Box>
        </Stack>
      </ListItemText>
    </ListItemButton>
  );
};


export default ContactListItem;
