import pandas as pd # type: ignore
import numpy as np

def get_landmarks_df(landmarks):

    num_landmarks = len(landmarks) // 2
    data = {'x': landmarks[::2], 'y': landmarks[1::2]}
    df = pd.DataFrame(data, columns=[f'landmark_{i}_x' if i % 2 == 0 else f'landmark_{i//2}_y' for i in range(num_landmarks * 2)])
    return df